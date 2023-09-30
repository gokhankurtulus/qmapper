<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:40
 */


namespace QMapper\Core;

use QMapper\Enums\DatabaseDriver;
use QMapper\Interfaces\IBuilder;

abstract class BuilderAgent
{
    /**
     * @var DatabaseDriver|null
     */
    protected static ?DatabaseDriver $driver = null;
    /**
     * @var IBuilder[]|null
     */
    protected static array|null $builders = null;

    /**
     * Configure the builder.
     */
    final protected static function configure(): void
    {
        if (is_null(static::getDriver()))
            static::setDriver(DatabaseDriver::tryFrom($_ENV['DB_DEFAULT_DRIVER']));
        static::setBuilder(static::getDriver()?->getDriverBuilder());
    }

    /**
     * @return DatabaseDriver|null
     */
    final public static function getDriver(): ?DatabaseDriver
    {
        return static::$driver;
    }

    /**
     * @param DatabaseDriver|null $driver
     */
    final protected static function setDriver(?DatabaseDriver $driver): void
    {
        static::$driver = $driver;
    }

    /**
     * @return IBuilder|null
     */
    final protected static function getBuilder(): ?IBuilder
    {
        return static::$builders[static::class] ?? null;
    }

    /**
     * @param IBuilder|null $builder
     */
    final protected static function setBuilder(?IBuilder $builder): void
    {
        static::$builders[static::class] = $builder;
    }
}