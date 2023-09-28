<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 03:55
 */


namespace QMapper\Enums;

use QMapper\Core\Builders\MSSQLBuilder;
use QMapper\Core\Builders\MySQLBuilder;
use QMapper\Core\Builders\PostgreSQLBuilder;
use QMapper\Core\Builders\SQLiteBuilder;
use QMapper\Interfaces\IBuilder;
use QMapper\Core\Builders\MongoDBBuilder;
use QMapper\Core\Builders\PDOBuilder;

enum DatabaseDriver: string
{

    case MySQL = 'mysql';
    case MSSQL = 'sqlsrv';
    case PostgreSQL = 'pgsql';
    case SQLite = 'sqlite';
    case MongoDB = 'mongodb';

    public function getDriverBuilder(): IBuilder
    {
        return match ($this) {
            self::MySQL => new MySQLBuilder(),
            self::MSSQL => new MSSQLBuilder(),
            self::PostgreSQL => new PostgreSQLBuilder(),
            self::SQLite => new SQLiteBuilder(),
            self::MongoDB => new MongoDBBuilder()
        };
    }
}