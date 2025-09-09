# Laravel Factory System - Complete API Documentation

Based on comprehensive research of Laravel's Eloquent Factory system including documentation and source code analysis.

## Overview

Laravel's Factory system provides a powerful way to generate test data and seed databases. Factories are located in `database/factories` directory and extend the base `Illuminate\Database\Eloquent\Factories\Factory` class.

## Class Structure

**Namespace:** `Illuminate\Database\Eloquent\Factories`  
**Type:** Abstract class  
**Template:** `@template TModel of \Illuminate\Database\Eloquent\Model`

### Traits Used
- `Illuminate\Support\Traits\Conditionable`
- `Illuminate\Support\Traits\ForwardsCalls` 
- `Illuminate\Support\Traits\Macroable`

## Properties

### Static Properties
- `public static $namespace = 'Database\\Factories\\'`
- `protected static $modelNameResolver`
- `protected static $modelNameResolvers = []`
- `protected static $factoryNameResolver`

### Instance Properties
- `protected $model` - The model class being factored
- `protected $count` - Number of models to create
- `protected $states` - State transformations
- `protected $has` - Child relationships
- `protected $for` - Parent relationships
- `protected $recycle` - Models to recycle for relationships
- `protected $afterMaking` - After making callbacks
- `protected $afterCreating` - After creating callbacks
- `protected $connection` - Database connection
- `protected $faker` - Faker instance
- `protected $expandRelationships = true`

## Public API Methods

### Core Creation Methods

#### `abstract public function definition(): array`
- **Purpose**: Define default model attributes
- **Parameters**: None
- **Returns**: `array` - Default attributes for the model
- **Notes**: Must be implemented by all factory subclasses

#### `public static function new(array $attributes = []): static`
- **Purpose**: Create new factory instance
- **Parameters**: `$attributes` - Override attributes
- **Returns**: `static` - New factory instance
- **Usage**: `User::factory()->new(['name' => 'John'])`

#### `public static function times(int $count): static`
- **Purpose**: Create factory for specified number of models
- **Parameters**: `$count` - Number of models to create
- **Returns**: `static` - Factory instance with count set
- **Usage**: `User::factory()->times(5)`

#### `public function create($attributes = [], ?Model $parent = null)`
- **Purpose**: Create and persist model(s) to database
- **Parameters**: 
  - `$attributes` - Override attributes (array or Collection)
  - `$parent` - Parent model for relationships
- **Returns**: `Model|Collection` - Single model or collection of models
- **Usage**: `User::factory()->create(['email' => 'test@example.com'])`

#### `public function createOne($attributes = []): Model`
- **Purpose**: Create single model instance and persist to database
- **Parameters**: `$attributes` - Override attributes
- **Returns**: `Model` - Single model instance
- **Usage**: `User::factory()->createOne()`

#### `public function createOneQuietly($attributes = []): Model`
- **Purpose**: Create single model without firing events
- **Parameters**: `$attributes` - Override attributes
- **Returns**: `Model` - Single model instance
- **Usage**: `User::factory()->createOneQuietly()`

#### `public function createMany(iterable $records): Collection`
- **Purpose**: Create multiple models from array of attributes
- **Parameters**: `$records` - Iterable of attribute arrays
- **Returns**: `Collection` - Collection of created models
- **Usage**: `User::factory()->createMany([['name' => 'John'], ['name' => 'Jane']])`

#### `public function createQuietly($attributes = [], ?Model $parent = null)`
- **Purpose**: Create model(s) without firing events
- **Parameters**: 
  - `$attributes` - Override attributes
  - `$parent` - Parent model
- **Returns**: `Model|Collection` - Model(s) without events
- **Usage**: `User::factory()->createQuietly()`

#### `public function make($attributes = [], ?Model $parent = null)`
- **Purpose**: Create model instance(s) without persisting to database
- **Parameters**: 
  - `$attributes` - Override attributes
  - `$parent` - Parent model
- **Returns**: `Model|Collection` - Unpersisted model(s)
- **Usage**: `User::factory()->make(['name' => 'Test'])`

#### `public function makeOne($attributes = []): Model`
- **Purpose**: Create single model instance without persisting
- **Parameters**: `$attributes` - Override attributes
- **Returns**: `Model` - Single unpersisted model
- **Usage**: `User::factory()->makeOne()`

### State Management

#### `public function state($state): static`
- **Purpose**: Add state transformation to modify attributes
- **Parameters**: `$state` - Closure, callable, or array for state modification
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->state(['is_admin' => true])`

#### `public function sequence(...$sequence): static`
- **Purpose**: Add sequenced state transformations
- **Parameters**: `...$sequence` - Variable arguments for sequence values
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->count(3)->sequence(['role' => 'admin'], ['role' => 'user'])`

#### `public function crossJoinSequence(...$sequence): static`
- **Purpose**: Add cross-joined sequenced state
- **Parameters**: `...$sequence` - Variable arguments for cross-join sequence
- **Returns**: `static` - Factory instance
- **Usage**: Complex sequencing across multiple attributes

### Relationship Methods

#### `public function has(Factory $factory, $relationship = null): static`
- **Purpose**: Define child relationship (hasMany, hasOne)
- **Parameters**: 
  - `$factory` - Child factory instance
  - `$relationship` - Relationship method name (optional)
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->has(Post::factory()->count(3), 'posts')`

#### `public function hasAttached($factory, $pivot = [], $relationship = null): static`
- **Purpose**: Define attached many-to-many relationship
- **Parameters**: 
  - `$factory` - Related factory
  - `$pivot` - Pivot table attributes
  - `$relationship` - Relationship name
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->hasAttached(Role::factory(), ['created_at' => now()])`

#### `public function for($parent, $relationship = null): static`
- **Purpose**: Define parent relationship (belongsTo)
- **Parameters**: 
  - `$parent` - Parent factory, model, or attributes
  - `$relationship` - Relationship method name
- **Returns**: `static` - Factory instance
- **Usage**: `Post::factory()->for(User::factory(), 'author')`

### Configuration Methods

#### `public function configure(): static`
- **Purpose**: Configure factory (template method for subclasses)
- **Parameters**: None
- **Returns**: `static` - Factory instance
- **Notes**: Can be overridden in factory subclasses

#### `public function connection(string $connection): static`
- **Purpose**: Set database connection for model creation
- **Parameters**: `$connection` - Database connection name
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->connection('testing')`

#### `public function recycle($models): static`
- **Purpose**: Set models to recycle for relationships instead of creating new ones
- **Parameters**: `$models` - Models or collection to reuse
- **Returns**: `static` - Factory instance
- **Usage**: `Post::factory()->recycle($users)`

### Callback Methods

#### `public function afterMaking(Closure $callback): static`
- **Purpose**: Add callback to run after making model (before persistence)
- **Parameters**: `$callback` - Closure to execute
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->afterMaking(function ($user) { $user->slug = Str::slug($user->name); })`

#### `public function afterCreating(Closure $callback): static`
- **Purpose**: Add callback to run after creating model (after persistence)
- **Parameters**: `$callback` - Closure to execute
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->afterCreating(function ($user) { $user->sendWelcomeEmail(); })`

### Utility Methods

#### `public function count(int $count): static`
- **Purpose**: Set number of models to create
- **Parameters**: `$count` - Number of models
- **Returns**: `static` - Factory instance
- **Usage**: `User::factory()->count(10)`

#### `public function raw($attributes = [], ?Model $parent = null): mixed`
- **Purpose**: Get raw attributes without creating model instance
- **Parameters**: 
  - `$attributes` - Override attributes
  - `$parent` - Parent model
- **Returns**: `array|array[]` - Raw attributes or array of attribute arrays
- **Usage**: `User::factory()->raw()` returns `['name' => 'John', 'email' => 'john@example.com']`

## Static Configuration Methods

#### `public static function guessModelNamesUsing(callable $callback): void`
- **Purpose**: Set callback for guessing model names from factory names
- **Parameters**: `$callback` - Model name resolver function
- **Usage**: Custom model name resolution logic

#### `public static function guessFactoryNamesUsing(callable $callback): void`
- **Purpose**: Set callback for guessing factory names from relationships
- **Parameters**: `$callback` - Factory name resolver function
- **Usage**: Custom factory discovery logic

#### `public static function useNamespace(string $namespace): void`
- **Purpose**: Set default namespace for factories
- **Parameters**: `$namespace` - Factory namespace
- **Usage**: `Factory::useNamespace('App\\Factories\\')`

#### `public static function factoryForModel(string $modelName): Factory`
- **Purpose**: Get factory instance for given model name
- **Parameters**: `$modelName` - Full model class name
- **Returns**: `Factory` - Factory instance for the model
- **Usage**: Factory discovery mechanism

## Key Design Patterns

1. **Fluent Interface**: All configuration methods return `$this` for method chaining
2. **Template Pattern**: Abstract `definition()` method must be implemented by subclasses
3. **Factory Pattern**: Creates objects without specifying exact class
4. **Builder Pattern**: Builds complex objects step by step through method chaining
5. **Reflection Usage**: Uses reflection for model instantiation and property access

## Integration Features

- **Faker Integration**: Automatic integration with Faker library for realistic test data
- **Eloquent Models**: Deep integration with Eloquent ORM and model events
- **Database Connections**: Support for multiple database connections
- **Event System**: Integration with model events (can be silenced with "quietly" methods)
- **Collections**: Returns Eloquent Collections for multiple models
- **Relationship Support**: Full support for all Eloquent relationship types

## Usage Patterns

### Basic Factory
```php
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];
    }
}
```

### State Usage
```php
User::factory()->state(['is_admin' => true])->create();
```

### Sequences
```php
User::factory()
    ->count(3)
    ->sequence(
        ['role' => 'admin'],
        ['role' => 'user'],
        ['role' => 'moderator']
    )
    ->create();
```

### Relationships
```php
// Has Many
User::factory()
    ->has(Post::factory()->count(3))
    ->create();

// Belongs To
Post::factory()
    ->for(User::factory(), 'author')
    ->create();

// Many to Many
User::factory()
    ->hasAttached(Role::factory()->count(2))
    ->create();
```

This comprehensive API documentation covers all public methods, their parameters, return types, and usage examples for Laravel's Factory system.