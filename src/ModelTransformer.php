<?php

namespace Nolanos\LaravelModelTypescriptTransformer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Transformers\Transformer;

class ModelTransformer implements Transformer
{
    public function transform(ReflectionClass $class, string $name): TransformedType|null
    {
        if (!is_subclass_of($class->name, Model::class)) {
            return null;
        }
        /** @var Model $modelInstance */
        $modelInstance = $class->newInstanceWithoutConstructor();

        $table = $modelInstance->getTable();

        $hidden = $modelInstance->getHidden();
        $casts = $modelInstance->getCasts();

        $columns = Schema::getColumns($table);
        $columnNames = array_map(fn($col) => $col['name'], $columns);

        $serializedColumnNames = array_diff($columnNames, $hidden);

        $typescriptProperties = [];

        foreach ($serializedColumnNames as $index => $propertyName) {
            $column = $columns[$index];
            $isNullable = $column['nullable'];

            if (array_key_exists($propertyName, $casts)) {
                $typescriptType = $this->mapCastToType($casts[$propertyName]);
            } else {
                $typescriptType = $this->mapTypeNameToJsonType($column['type_name']);
            }

            $typescriptPropertyDefinition = "$propertyName: $typescriptType";

            if ($isNullable) {
                $typescriptPropertyDefinition .= ' | null';
            }

            $typescriptProperties[] = $typescriptPropertyDefinition;
        }

        return TransformedType::create(
            $class,
            $name,
            "{\n" . implode("\n", $typescriptProperties) . "\n}",
        );
    }

    private function mapTypeNameToJsonType(string $columnType): string
    {
        // Map Laravel column types to TypeScript types
        return match ($columnType) {
            // Strings
            'uuid', 'string', 'text', 'varchar', 'character varying', 'date', 'datetime', 'timestamp', 'timestamp without time zone', 'bpchar', 'timestamptz', 'time', 'bytea', 'blob' => 'string',
            // Numbers
            'integer', 'bigint', 'int2', 'int4', 'int8', 'float', 'double', 'decimal', 'float8', 'numeric' => 'number',
            // Booleans
            'boolean', 'bool' => 'boolean',
            // Unknown
            default => 'unknown /* ' . $columnType . ' */', // Fallback for other types
        };
    }

    private function mapCastToType(string $cast): string
    {
        return match ($cast) {
            'boolean' => 'boolean',
            'int', 'float' => 'number',
            'string', 'datetime', 'timestamp', 'date', 'uuid' => 'string',
            'array', 'object' => 'any',
            'collection' => 'any[]',
            default => 'unknown /* ' . $cast . ' */',
        };
    }
}
