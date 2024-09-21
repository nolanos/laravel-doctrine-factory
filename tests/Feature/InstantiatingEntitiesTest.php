<?php

namespace Tests\Feature;


use Illuminate\Support\Collection;
use Workbench\App\Entities\User;

/**
 * ---------------------------------------------------------------------------------
 * Instantiating Entities
 * ---------------------------------------------------------------------------------
 *
 * @see https://laravel.com/docs/11.x/eloquent-factories#instantiating-models
 */
describe('Instantiating Entitites', function () {
    test("make", function () {
        $entity = User::factory()->make();

        expect($entity)->toBeInstanceOf(User::class);
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
    })->todo(issue: 1);
});
