<?php

namespace Nolanos\LaravelDoctrineFactory;

use Doctrine\Common\Collections\Collection;
use Illuminate\Database\Eloquent\Factories\Relationship;

/**
 * Doctrine handles ManyToMany relationships differently than Eloquent.
 *
 * This class handles ManyToMany relationships by managing the collection
 * on both sides of the relationship.
 */
class DoctrineManyToManyRelationship extends Relationship
{
    /**
     * Create the many-to-many relationship for the given parent model.
     *
     * @param $parent
     * @return void
     */
    public function createFor($parent)
    {
        $reflectionClass = new \ReflectionClass(get_class($parent));
        $reflectionCollection = $reflectionClass->getProperty($this->relationship);
        $reflectionCollection->setAccessible(true);
        /** @var Collection $relationship */
        $relationship = $reflectionCollection->getValue($parent);

        $output = $this->factory->create();

        $output->each(function ($entity) use ($relationship, $parent) {
            $relationship->add($entity);
            
            // For ManyToMany relationships, we also need to add the parent to the child's collection
            // to maintain bidirectional consistency
            $this->addToInverseRelationship($entity, $parent);
        });
    }

    /**
     * Add the parent entity to the child's inverse relationship collection.
     *
     * @param $childEntity
     * @param $parentEntity
     * @return void
     */
    private function addToInverseRelationship($childEntity, $parentEntity): void
    {
        try {
            $childReflectionClass = new \ReflectionClass(get_class($childEntity));
            
            // Try to find the inverse relationship property
            // In a ManyToMany relationship, we need to determine the inverse property name
            $inversePropertyName = $this->guessInversePropertyName($parentEntity);
            
            if ($childReflectionClass->hasProperty($inversePropertyName)) {
                $childReflectionCollection = $childReflectionClass->getProperty($inversePropertyName);
                $childReflectionCollection->setAccessible(true);
                /** @var Collection $childRelationship */
                $childRelationship = $childReflectionCollection->getValue($childEntity);
                
                if (!$childRelationship->contains($parentEntity)) {
                    $childRelationship->add($parentEntity);
                }
            }
        } catch (\Exception $e) {
            // If we can't find or set the inverse relationship, just continue
            // This allows for unidirectional ManyToMany relationships
        }
    }

    /**
     * Guess the inverse property name based on the parent entity class.
     *
     * @param $parentEntity
     * @return string
     */
    private function guessInversePropertyName($parentEntity): string
    {
        $className = class_basename(get_class($parentEntity));
        return \Illuminate\Support\Str::plural(\Illuminate\Support\Str::camel($className));
    }
}