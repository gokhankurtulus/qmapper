<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 03:55
 */


namespace QMapper\Enums;

use QMapper\Core\Builders\MySQLBuilder;
use QMapper\Traits\NullSupportedEnum;

enum DataDriver: string
{
    use NullSupportedEnum;

    case MySQL = 'mysql';
    case SQLite = 'sqlite';
    case PostgreSQL = 'pgsql';

    public function getDriverBuilder(): MySQLBuilder
    {
        return match ($this) {
            self::MySQL => new MySQLBuilder()
        };
    }
}