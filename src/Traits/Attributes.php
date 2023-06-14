<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 10.06.2023 Time: 04:29
 */


namespace QMapper\Traits;

trait Attributes
{
    protected static string $table = '';
    protected static string $primaryKey = '';

    protected static mixed $builderStatement = null;

    /**
     * @return string
     */
    public static function getTable(): string
    {
        return static::$table;
    }

    /**
     * @return string
     */
    public static function getPrimaryKey(): string
    {
        return static::$primaryKey;
    }

    /**
     * @return mixed|null
     */
    public static function getBuilderStatement(): mixed
    {
        return static::$builderStatement;
    }

    /**
     * @param mixed|null $builderStatement
     */
    public static function setBuilderStatement(mixed $builderStatement): void
    {
        static::$builderStatement = $builderStatement;
    }

    /**
     * @return bool
     */
    public function checkNecessaryModelProperties(): bool
    {
        return static::getTable() && static::getPrimaryKey();
    }
}