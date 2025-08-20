# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a Laravel package that provides Doctrine Factory support for Laravel. It is intended to be a drop-in replacement
for Eloquent factories that works with Doctrine entities. The package extends Laravel's
`Illuminate\Database\Eloquent\Factories\Factory` to work with Doctrine ORM instead of Eloquent models.

## Essential Commands

### Testing

```bash
composer test              # Run all tests using Pest
vendor/bin/pest           # Direct Pest command
```

### Linting & Static Analysis

```bash
composer lint             # Run PHPStan static analysis
vendor/bin/phpstan analyse --verbose --ansi
```

### Package Development

```bash
composer install          # Install dependencies
composer clear            # Clear testbench skeleton
composer prepare          # Discover packages
composer build            # Build workbench
composer serve            # Start testbench server
```

## Architecture

### Core Classes

**`DoctrineFactory`** (`src/DoctrineFactory.php`) - The main factory class that extends Laravel's Factory:

- Overrides `create()`, `make()`, `newModel()` to work with Doctrine entities instead of Eloquent models
- Uses reflection to instantiate entities, handling constructor parameters and private properties
- Integrates with Doctrine's EntityManager for persistence instead of Eloquent's save methods
- Supports entity relationships through custom relationship classes

**`DoctrineRelationship`** (`src/DoctrineRelationship.php`) - Handles child relationships:

- Manages one-to-many and many-to-many relationships using Doctrine Collections
- Creates child entities and adds them to parent collection properties

**`DoctrineBelongsToRelationship`** (`src/DoctrineBelongsToRelationship.php`) - Handles parent relationships:

- Manages belongs-to relationships by setting entity references instead of foreign keys
- Returns whole entity instances rather than IDs for Doctrine's relationship mapping

**`MissingConstructorAttributesException`** - Custom exception for missing required constructor parameters

### Key Design Patterns

1. **Reflection-based instantiation**: Uses PHP reflection to create entities, handle private properties, and manage
   constructor parameters
2. **Entity relationship mapping**: Maps Laravel factory relationships to Doctrine entity relationships
3. **Collection management**: Uses Doctrine Collections instead of Eloquent Collections for relationships
4. **Persistence integration**: Uses EntityManager::persist() and flush() instead of model save methods

### Workbench Structure

The `workbench/` directory contains a test Laravel application:

- `app/Entities/` - Example Doctrine entities (User, Post, Comment)
- `database/factories/Entities/` - Example factories extending DoctrineFactory
- `database/migrations/` - Database migrations for test entities

### Testing

- Uses Pest PHP testing framework
- Tests are organized in `tests/Feature/` and `tests/Unit/`
- Test configuration uses Orchestra Testbench with SQLite in-memory database
- Key test files:
    - `InstantiatingEntitiesTest.php` - Tests entity creation and constructor handling
    - `PersistingEntitiesTest.php` - Tests database persistence
    - `Relationships/` - Tests relationship handling

## Development Notes

- The package maintains Laravel Factory API compatibility - existing factory code should work with minimal changes
- Entity constructors are properly handled - required parameters must be provided in factory definitions
- Private entity properties are accessible via reflection for testing and factory population
- Doctrine Collections are used throughout for relationship management
- EntityManager is used for all persistence operations instead of Eloquent model methods