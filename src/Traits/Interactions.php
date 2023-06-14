<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 10.06.2023 Time: 04:26
 */


namespace QMapper\Traits;

trait Interactions
{

    public static function get()
    {
        static::setBuilderStatement(static::getBuilderStatement()->build());
        return static::getBuilderStatement()['data'] ?? null;
    }

    public static function rowCount()
    {
        return static::getBuilderStatement()['row_count'] ?? null;
    }

    public static function where(array ...$where): static
    {
        if (!static::getBuilderStatement())
            static::setBuilderStatement(static::getDBMSBuilder()->select()->from(static::getTable()));
        static::setBuilderStatement(static::getBuilderStatement()->where('AND', ...$where));
        return new static();
    }

    public static function orWhere(array ...$orWhere): static
    {
        static::setBuilderStatement(static::getBuilderStatement()->orWhere('AND', ...$orWhere));
        return new static();
    }

    public static function order(array ...$sort)
    {
        static::setBuilderStatement(static::getBuilderStatement()->orderBy(...$sort));
        return new static();
    }

    public static function limit(?int $limit)
    {
        static::setBuilderStatement(static::getBuilderStatement()->limit($limit));
        return new static();
    }

    public static function offset(?int $offset)
    {
        static::setBuilderStatement(static::getBuilderStatement()->offset($offset));
        return new static();
    }
}