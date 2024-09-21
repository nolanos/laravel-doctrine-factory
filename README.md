# Laravel Doctrine Factory

Make and create Doctrine entities in your Laravel tests.

## Installation

Install via Composer:

```bash
composer require nolanos/laravel-doctrine-factory
```

## Usage

Create Laravel factories and extend `Nolanos\LaravelDoctrineFactory\DoctrineFactory` instead of the
usual `Illuminate\Database\Eloquent\Factories\Factory`.

`DoctrineFactory` subclasses the default `Factory` to override how it instantiates and
saves the objects. Everything else works exactly the same.

## Design Philosophy

The goal of this package is to provide a drop-in replacement for Laravel's default
factories that works with Doctrine entities. It should mirror the existing API
so closely that you could read the Laravel documentation and use this package.s

# Development

### Setup

```bash
git clone git@github.com:nolanos/laravel-doctrine-factory.git

cd laravel-doctrine-factory

composer install
```

### Running Tests

```bash

composer test
```

### Publishing new Versions

To publish a new version of the package, you need to create a new tag and push it to the repository.

```bash
git tag vx.x.x
git push origin vx.x.x
```

Go to [Packagist](https://packagist.org/packages/nolanos/laravel-doctrine-factory) and click on "Update" to
update the package.
