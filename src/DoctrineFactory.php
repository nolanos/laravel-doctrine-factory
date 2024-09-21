<?php

namespace Nolanos\LaravelDoctrineFactory;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @param  \Object|null  $parent
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
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>|TModel
     */
    public function make($attributes = [], ?Model $parent = null)
    {
        if (! empty($attributes)) {
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
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function newModel(array $attributes = [])
    {
        $reflectionClass = new ReflectionClass($this->modelName());

        $instance = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($attributes as $attribute => $value) {
            $reflectionProperty = $reflectionClass->getProperty($attribute);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $value);
        }

        return $instance;
    }


    /**
     * Set the connection name on the results and store them.
     *
     * @param  \Illuminate\Support\Collection<int, TModel>  $results
     * @return void
     */
    protected function store(Collection $results)
    {
        $results->each(function ($model) {
            EntityManager::persist($model);
        });

        EntityManager::flush();
    }

    /**
     * Define a parent relationship for the entity.
     *
     * TODO: Accept a Factory as well as a ready made Entity
     *
     * @param object $entity A Doctrine entity
     * @param string|null $relationship The relationship name to use. Defaults to the entity name in camelCase.
     * @return static
     */
    public function for($entity, $relationship = null)
    {
        return $this->state(function (array $attributes) use ($entity, $relationship) {
            $relationship = $relationship ?? lcfirst(class_basename($entity));

            return [
                $relationship => $entity,
            ];
        });
    }
}
