<?php

namespace Nolanos\LaravelDoctrineFactory;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use ReflectionClass;

abstract class DoctrineFactory extends Factory
{
    /**
     * Create a collection of entities and persist them to the database.
     * 
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @param  object|null  $parent
     * @return \Illuminate\Database\Eloquent\Collection<int, object>|object
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        if (! empty($attributes)) {
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
     * Instantiates the Entity with the given attributes.
     *
     * @param array $attributes
     * @return object|string
     * @throws \ReflectionException
     */
    public function newModel(array $attributes = [])
    {
        $reflectionClass = new ReflectionClass($this->modelName());

        $instance = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($attributes as $attribute => $value) {

            $reflectionProperty = $reflectionClass->getProperty($attribute);
            // Make private/protected properties accessible
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $value);
        }

        return $instance;
    }

    /**
     * Store the given models in the database.
     * 
     * @param Illuminate\Support\Collection $results
     * @return void
     */
    public function store(Collection $results)
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
