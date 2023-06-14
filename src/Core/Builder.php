<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:40
 */


namespace QMapper\Core;

use QMapper\Enums\DataDriver;
use QMapper\Exceptions\BuilderException;
use QMapper\Interfaces\BuilderInterface;

class Builder
{
    private static ?DataDriver $dataDriver;
    private static ?BuilderInterface $DBMSBuilder;

    /**
     * @throws BuilderException
     */
    public function __construct()
    {
        static::setDataDriver(DataDriver::tryFromNotNull($_ENV['DB_DRIVER']));
        static::setDBMSBuilder(self::getDataDriver()?->getDriverBuilder());
        if (!static::getDBMSBuilder())
            throw new BuilderException('Builder is not set correctly.');
    }

    /**
     * @return DataDriver|null
     */
    public static function getDataDriver(): ?DataDriver
    {
        return static::$dataDriver;
    }

    /**
     * @param DataDriver|null $dataDriver
     */
    public static function setDataDriver(?DataDriver $dataDriver): void
    {
        static::$dataDriver = $dataDriver;
    }

    /**
     * @return BuilderInterface|null
     */
    public static function getDBMSBuilder(): ?BuilderInterface
    {
        return static::$DBMSBuilder;
    }

    /**
     * @param BuilderInterface|null $DBMSBuilder
     */
    public static function setDBMSBuilder(?BuilderInterface $DBMSBuilder): void
    {
        static::$DBMSBuilder = $DBMSBuilder;
    }
}