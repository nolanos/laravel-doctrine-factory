<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Nolanos\LaravelDoctrineFactory\DoctrineFactory;
use Workbench\App\Entities\User;

/**
 * ---------------------------------------------------------------------------------
 * Instantiating Entities
 * ---------------------------------------------------------------------------------
 *
 * @see https://laravel.com/docs/11.x/eloquent-factories#instantiating-models
 */
describe('Instantiating Entities', function () {
    test("make", function () {
        $entity = User::factory()->make();

        expect($entity)->toBeInstanceOf(User::class)
            ->and(EntityManager::contains($entity))->toBeFalse();
    });

    test("overriding attributes", function () {
        $name = 'Billy the Kid';

        $entity = User::factory()->make(['name' => $name]);

        expect($entity)->getName()->toBe($name);
    });

    test("overriding attributes using the factory state", function () {
        $name = 'Billy the Kid';

        $entity = User::factory()->state(['name' => $name])->make();

        expect($entity)->getName()->toBe($name);
    });

    test("make multiple", function () {
        $users = User::factory()->count(3)->make();

        expect($users)
            ->toHaveCount(3)
            ->toBeInstanceOf(Collection::class);
    });

    test("attributes are set through the constructor", function () {
        class NamedThing
        {
            private string $name;

            public function __construct(string $name)
            {
                $this->name = $name . ' was set through the constructor';
            }

            public function getName()
            {
                return $this->name;
            }
        }

        class NamedThingFactory extends DoctrineFactory
        {
            protected $model = NamedThing::class;

            public function definition(): array
            {
                return [];
            }
        }

        // Constructor is called with name from $attributes so that
        // the name is modified and set through the constructor.
        expect(NamedThingFactory::new()->make(['name' => 'Joe']))
            ->getName()->toBe('Joe was set through the constructor');
    });

    test("other attributes are set after the constructor", function () {
        class CoolThing
        {
            private bool $isCool;

            public function __construct()
            {
                $this->isCool = false;
            }

            public function isCool(): bool
            {
                return $this->isCool;
            }
        }

        class CoolThingFactory extends DoctrineFactory
        {
            protected $model = CoolThing::class;

            public function definition(): array
            {
                return [];
            }
        }

        // Constructor runs to set the default
        expect(CoolThingFactory::new()->make())
            ->isCool()->toBeFalse();

        // The default is overridden from the attributes
        expect(CoolThingFactory::new()->make(['isCool' => true]))
            ->isCool()->toBeTrue();
    });

    test("supports entities without constructors", function () {
        class Constructorless
        {
            private string $name = 'unnamed';

            public function getName()
            {
                return $this->name;
            }
        }

        class ConstructorlessFactory extends DoctrineFactory
        {
            protected $model = Constructorless::class;

            public function definition(): array
            {
                return [];
            }
        }

        // Constructor is called with name from $attributes so that
        // the name is modified and set through the constructor.
        expect(ConstructorlessFactory::new()->make(['name' => 'George']))
            ->getName()->toBe('George');
    });
});
