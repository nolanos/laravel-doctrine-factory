<?php

namespace Nolanos\LaravelDoctrineFactory;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelDoctrine\ORM\Facades\EntityManager;
use ReflectionClass;


/**
 * @template TModel
 *
 * @method $this trashed()
 */
abstract class DoctrineFactory extends Factory
{
    /**
     * Create a collection of models and persist them to the database.
     *
     * @override This method flips the conditional around. The original checked if the result was an
     * instance of Model. With Doctrine there is no common superclass, so we instead check if the
     * result is a Collection.
     *
     * @param (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed> $attributes
     * @param \Object|null $parent
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>|TModel
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        if (!empty($attributes)) {
            return $this->state($attributes)->create([], $parent);
        }

        $results = $this->make($attributes, $parent);

        if ($results instanceof Collection) {
            $this->store($results);

            $this->callAfterCreating($results, $parent);
        } else {
            $this->store(collect([$results]));

            $this->callAfterCreating(collect([$results]), $parent);
        }

        return $results;
    }

    /**
     * Create a collection of models.
     *
     * @override This method is exactly the same, except it directly creates new collections
     * instead of going through the `newModel()->newCollection()` method that is used in the
     * Eloquent factories.
     *
     * @param (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed> $attributes
     * @param \Illuminate\Database\Eloquent\Model|null $parent
     * @return \Illuminate\Support\Collection<int, TModel>|TModel
     */
    public function make($attributes = [], ?Model $parent = null)
    {
        if (!empty($attributes)) {
            return $this->state($attributes)->make([], $parent);
        }

        if ($this->count === null) {
            return tap($this->makeInstance($parent), function ($instance) {
                $this->callAfterMaking(collect([$instance]));
            });
        }

        if ($this->count < 1) {
            /**
             * @modified Directly calls `collect` instead of doing $this->newModel()->newCollection()
             */
            return collect(); // Re
        }

        /**
         * @modified Directly calls `collect` instead of doing $this->newModel()->newCollection()
         */
        $instances = collect(array_map(function () use ($parent) {
            return $this->makeInstance($parent);
        }, range(1, $this->count)));

        $this->callAfterMaking($instances);

        return $instances;
    }

    /**
     * Get a new model instance.
     *
     * @override Eloquent model constructors have a consistent API, but that is not
     * necessarily the case for Doctrine entities. This method is overridden to
     * use reflection to create a new instance without using the constructor, and
     * then set the properties directly–even if they are private.
     *
     * @param array<string, mixed> $attributes
     * @return TModel
     */
    public function newModel(array $attributes = [])
    {
        $reflectionClass = new ReflectionClass($this->modelName());

        // Initialize object using the constructor
        $constructorArgs = [];

        if ($constructor = $reflectionClass->getConstructor()) {
            foreach ($constructor->getParameters() as $param) {
                if (array_key_exists($param->getName(), $attributes)) {
                    $constructorArgs[$param->getName()] = $attributes[$param->getName()];
                    unset($attributes[$param->getName()]);
                }
            }
        }

        $instance = $reflectionClass->newInstanceArgs($constructorArgs);

        // Update every non-constructor attribute directly
        foreach ($attributes as $attribute => $value) {
            if ($reflectionClass->hasProperty($attribute)) {
                $reflectionProperty = $reflectionClass->getProperty($attribute);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($instance, $value);
            }
        }

        return $instance;
    }

    /**
     * Set the connection name on the results and store them.
     *
     * @override This method is overridden to use the `EntityManager` facade to persist the models
     * rather than the `Model::create` method that is used in the Eloquent factories.
     *
     * @param \Illuminate\Support\Collection<int, TModel> $results
     * @return void
     */
    protected function store(Collection $results)
    {
        $results->each(function ($model) {
            EntityManager::persist($model);

            $this->createChildren($model);
        });

        EntityManager::flush();
    }

    /**
     * Create the children for the given model.
     *
     * @param  $model
     * @return void
     */
    protected function createChildren($model)
    {
        $this->has->each(function ($has) use ($model) {
            $has->recycle($this->recycle)->createFor($model);
        });
    }

    /**
     * Define a parent relationship for the model.
     *
     * @override This method is overridden to add an instance of `DoctrineBelongsToRelationship`
     * to the `for` property, rather then the `BelongsToRelationship` class that is used in the
     * Eloquent factories.
     *
     * @param \Illuminate\Database\Eloquent\Factories\Factory|\Illuminate\Database\Eloquent\Model $factory
     * @param string|null $relationship
     * @return static
     */
    public function for($factory, $relationship = null)
    {
        return $this->newInstance(['for' => $this->for->concat([new DoctrineBelongsToRelationship(
            $factory,
            $relationship ?? Str::camel(class_basename(
            $factory instanceof Factory ? $factory->modelName() : $factory
        ))
        )])]);
    }

    /**
     * Define a child relationship for the model.
     *
     * @param DoctrineFactory $factory
     * @param string|null $relationship
     * @return static
     */
    public function has($factory, $relationship = null): static
    {
        $guessRelationship = Str::plural($this->guessRelationship($factory->modelName()));
        $doctrineRelationship = new DoctrineRelationship(
            $factory, $relationship ?? $guessRelationship
        );
        return $this->newInstance([
            'has' => $this->has->concat([$doctrineRelationship]),
        ]);
    }

    /**
     * Proxy dynamic factory methods onto their proper methods.
     *
     * @override To handle the `for` method for Doctrine entities. Assumes that the factory
     * is nested within the Entities namespace.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'for')) {
            $relationship = Str::camel(Str::after($method, 'for'));

            $factoryName = static::$namespace . 'Entities\\' . Str::studly($relationship) . 'Factory';

            $factory = new $factoryName;

            return $this->for($factory->state($parameters[0] ?? []), $relationship);
        }

        if (Str::startsWith($method, 'has')) {
            $relationship = Str::camel(Str::after($method, 'has'));

            $factoryName = static::$namespace . 'Entities\\' . Str::singular(Str::studly($relationship)) . 'Factory';

            $factory = new $factoryName;

            return $this->has($factory->count($parameters[0]), $relationship);
        }

        return parent::__call($method, $parameters);
    }
}
