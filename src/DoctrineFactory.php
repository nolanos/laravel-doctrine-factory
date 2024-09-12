<?php

namespace Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use ReflectionClass;

abstract class DoctrineFactory extends Factory
{
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
     * @return Model|object|string
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

    public function store(Collection $results)
    {

        $results->each(function ($model) {
            EntityManager::persist($model);
        });

        EntityManager::flush();
    }

}
