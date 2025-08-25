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
        
        // Create a realistic mock entity with a proper class name
        $mockEntity = new class {
            public function __construct()
            {
                // Mock Post entity
            }
        };
        
        // Set the class name to something more realistic for testing
        $realPost = new \Workbench\App\Entities\Post();
        
        $reflectionClass = new \ReflectionClass($relationship);
        $method = $reflectionClass->getMethod('guessInversePropertyName');
        $method->setAccessible(true);
        
        $result = $method->invoke($relationship, $realPost);
        
        // Should convert "Post" to "posts"
        expect($result)->toBe('posts');
    });
});