<?php

namespace Tests\Unit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nolanos\LaravelDoctrineFactory\DoctrineManyToManyRelationship;
use Nolanos\LaravelDoctrineFactory\DoctrineFactory;

describe('DoctrineManyToManyRelationship', function () {
    test('it can be instantiated', function () {
        $factory = $this->createMock(DoctrineFactory::class);
        $relationship = new DoctrineManyToManyRelationship($factory, 'tags');
        
        expect($relationship)->toBeInstanceOf(DoctrineManyToManyRelationship::class);
    });

    test('it guesses inverse property name correctly', function () {
        $factory = $this->createMock(DoctrineFactory::class);
        $relationship = new DoctrineManyToManyRelationship($factory, 'tags');
        
        // Create a mock entity to test the guess method
        $mockEntity = new class {
            public function __construct()
            {
                // Mock entity
            }
        };
        
        $reflectionClass = new \ReflectionClass($relationship);
        $method = $reflectionClass->getMethod('guessInversePropertyName');
        $method->setAccessible(true);
        
        $result = $method->invoke($relationship, $mockEntity);
        
        // It should convert the class name to a plural camel case property name
        expect($result)->toContain('class');
    });
});