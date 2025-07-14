<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

/**
 * Helper trait for handling PostgreSQL ENUMs in migrations
 */
trait PostgreSQLEnumHelper
{
    /**
     * Create an ENUM type in PostgreSQL
     */
    protected function createEnum(string $name, array $values): void
    {
        $valuesString = "'" . implode("', '", $values) . "'";
        DB::statement("CREATE TYPE {$name} AS ENUM ({$valuesString})");
    }

    /**
     * Drop an ENUM type in PostgreSQL
     */
    protected function dropEnum(string $name): void
    {
        DB::statement("DROP TYPE IF EXISTS {$name}");
    }

    /**
     * Add ENUM column using custom type
     */
    protected function addEnumColumn(Blueprint $table, string $column, string $enumType, ?string $default = null): void
    {
        if ($default !== null) {
            $table->addColumn('enum', $column, ['customSchemaType' => $enumType])->default($default);
        } else {
            $table->addColumn('enum', $column, ['customSchemaType' => $enumType]);
        }
    }
} 