<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 18:24
 */

namespace QMapper\Traits\Model;

use QMapper\Core\Builder;
use QMapper\Enums\DatabaseDriver;
use QMapper\Enums\SystemMessage;
use QMapper\Exceptions\ModelException;
use QMapper\Loggers\ModelLogger;

trait ModelAttributes
{
    protected static ?DatabaseDriver $defaultDriver = null;
    protected static ?DatabaseDriver $driver = null;
    protected static ?Builder $builder = null;
    protected static string $table = '';
    protected static string $indexKey = 'id';
    protected mixed $indexValue = '';
    protected array $properties = [];

    public static function getDefaultDriver(): ?DatabaseDriver
    {
        return static::$defaultDriver;
    }

    public static function setDefaultDriver(?DatabaseDriver $defaultDriver): void
    {
        static::$defaultDriver = $defaultDriver;
    }

    public static function getDriver(): ?DatabaseDriver
    {
        return static::$driver;
    }

    public static function setDriver(?DatabaseDriver $driver): void
    {
        static::$driver = $driver;
    }

    /**
     * @throws ModelException
     */
    public static function getBuilder(): ?Builder
    {
        if (!static::$builder)
            static::configure();
        return static::$builder;
    }

    public static function setBuilder(?Builder $builder): void
    {
        static::$builder = $builder;
    }

    /**
     * @throws ModelException
     */
    public static function configure(): void
    {
        if (!static::getDriver())
            static::setDriver(static::getDefaultDriver());
        if (!static::getDriver() instanceof DatabaseDriver) {
            ModelLogger::log(SystemMessage::FAILED_TO->get("configure database driver"), "Model's driver is not instance of DatabaseDriver");
            throw new ModelException(SystemMessage::FAILED_TO->get("configure database driver. Model's driver is not instance of DatabaseDriver"));
        }
        static::setBuilder(new Builder(static::getDriver()->getBuilder()));
    }

    /**
     * Returns table name of entity
     * @return string
     */
    public static function getTable(): string
    {
        return static::$table;
    }

    /**
     * Returns index key of entity
     * @return string
     */
    public static function getIndexKey(): string
    {
        return static::$indexKey;
    }

    /**
     * Get index value (primary key)
     * @return mixed
     */
    public function getIndexValue(): mixed
    {
        return $this->indexValue;
    }

    /**
     * Set index value (primary key)
     * @param mixed $indexValue
     * @return void
     */
    protected function setIndexValue(mixed $indexValue): void
    {
        $this->indexValue = $indexValue;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getProperty(string $key): mixed
    {
        return $this->properties[$key] ?? null;
    }

    public function setProperty(string $key, mixed $value): void
    {
        $this->properties[$key] = $value;
    }

    public function assignProperties(array|object $entityProperties = []): void
    {
        foreach ($entityProperties as $entityKey => $entityValue) {
            $this->setProperty($entityKey, $entityValue);
            if ($entityKey === static::getIndexKey())
                static::setIndexValue($entityValue);
        }
    }

    public static function getInstance(): self
    {
        return new static();
    }

    public function isEmpty(): bool
    {
        return empty($this->getProperties());
    }
}