<?php

namespace Tests\Unit;

use Nolanos\LaravelDoctrineFactory\DoctrineFactory;
use Workbench\App\Entities\Post;
use Workbench\App\Entities\Tag;

describe('ManyToMany Integration', function () {
    beforeEach(function () {
        DoctrineFactory::useNamespace('Workbench\\Database\\Factories\\');
    });

    test('can create Post factory with attachedTo configuration', function () {
        // This tests that the attachedTo method works without database operations
        $factory = Post::factory()->attachedTo(Tag::factory()->count(2));

        // Verify the factory has the ManyToMany relationships configured
        expect($factory)->toBeInstanceOf(DoctrineFactory::class);
        
        // Check that the has collection contains our ManyToMany relationships
        $reflection = new \ReflectionClass($factory);
        $hasProperty = $reflection->getProperty('has');
        $hasProperty->setAccessible(true);
        $hasRelationships = $hasProperty->getValue($factory);
        
        expect($hasRelationships)->toHaveCount(1);
        expect($hasRelationships->first())->toBeInstanceOf(\Nolanos\LaravelDoctrineFactory\DoctrineManyToManyRelationship::class);
    });

    test('can use magic method attachedToTags on factory', function () {
        $factory = Post::factory()->attachedToTags(3);

        expect($factory)->toBeInstanceOf(DoctrineFactory::class);
        
        $reflection = new \ReflectionClass($factory);
        $hasProperty = $reflection->getProperty('has');
        $hasProperty->setAccessible(true);
        $hasRelationships = $hasProperty->getValue($factory);
        
        expect($hasRelationships)->toHaveCount(1);
    });

    test('attachedTo method configures relationship correctly', function () {
        $factory = Post::factory()->attachedTo(Tag::factory()->state(['name' => 'Test Tag']));

        $reflection = new \ReflectionClass($factory);
        $hasProperty = $reflection->getProperty('has');
        $hasProperty->setAccessible(true);
        $hasRelationships = $hasProperty->getValue($factory);
        
        $relationship = $hasRelationships->first();
        expect($relationship)->toBeInstanceOf(\Nolanos\LaravelDoctrineFactory\DoctrineManyToManyRelationship::class);
        
        // Check that the relationship property name is correct
        $relationshipReflection = new \ReflectionClass($relationship);
        $relationshipProperty = $relationshipReflection->getProperty('relationship');
        $relationshipProperty->setAccessible(true);
        
        expect($relationshipProperty->getValue($relationship))->toBe('tags');
    });

    test('can create Post instance with make', function () {
        /** @var Post $post */
        $post = Post::factory()->make();

        expect($post)->toBeInstanceOf(Post::class);
        expect($post->getTags())->toHaveCount(0); // No relationships created with make()
    });

    test('Post and Tag entities have proper ManyToMany setup', function () {
        $post = new Post();
        $tag = new Tag('Test Tag');

        expect($post->getTags())->toBeInstanceOf(\Doctrine\Common\Collections\Collection::class);
        expect($tag->getPosts())->toBeInstanceOf(\Doctrine\Common\Collections\Collection::class);

        // Test bidirectional relationship methods
        $post->addTag($tag);
        
        expect($post->getTags())->toContain($tag);
        expect($tag->getPosts())->toContain($post);
    });
});