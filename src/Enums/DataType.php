<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 03:50
 */

namespace QMapper\Enums;
enum DataType: string
{
    case TINYINT = 'TINYINT';
    case SMALLINT = 'SMALLINT';
    case MEDIUMINT = 'MEDIUMINT';
    case INT = 'INT';
    case BIGINT = 'BIGINT';

    case DECIMAL = 'DECIMAL';
    case FLOAT = 'FLOAT';
    case DOUBLE = 'DOUBLE';

    case BOOLEAN = 'BOOLEAN';

    case CHAR = 'CHAR';
    case VARCHAR = 'VARCHAR';
    case TINYTEXT = 'TINYTEXT';
    case TEXT = 'TEXT';
    case MEDIUMTEXT = 'MEDIUMTEXT';
    case LONGTEXT = 'LONGTEXT';

    case ENUM = 'ENUM';

    case DATE = 'DATE';
    case DATETIME = 'DATETIME';
    case TIMESTAMP = 'TIMESTAMP';
    case TIME = 'TIME';
    case YEAR = 'YEAR';

    /**
     * Returns min lenght of selected data type
     * @return int|float|string|null
     */
    public function min(): int|float|string|null
    {
        return match ($this) {
            self::TINYINT => -128,
            self::SMALLINT => -32768,
            self::MEDIUMINT => -8388608,
            self::INT => -2147483648,
            self::BIGINT => -9223372036854775808,
            self::DECIMAL => -999999999999999.9999999999,
            self::FLOAT => -3.402823466E+38,
            self::DOUBLE => -1.7976931348623157E+308,
            self::BOOLEAN => 0,
            self::CHAR => 0,
            self::VARCHAR => 0,
            self::TINYTEXT => 0,
            self::TEXT => 0,
            self::MEDIUMTEXT => 0,
            self::LONGTEXT => 0,
            self::ENUM => 0,
            self::DATE => '1000-01-01',
            self::DATETIME => '1000-01-01 00:00:00',
            self::TIMESTAMP => '1970-01-01 00:00:01',
            self::TIME => '-838:59:59',
            self::YEAR => 1901,
        };
    }

    /**
     * Returns max lenght of selected data type
     * @return int|float|string|null
     */
    public function max(): int|float|string|null
    {
        return match ($this) {
            self::TINYINT => 127,
            self::SMALLINT => 32767,
            self::MEDIUMINT => 8388607,
            self::INT => 2147483647,
            self::BIGINT => 9223372036854775807,
            self::DECIMAL => 999999999999999.9999999999,
            self::FLOAT => 3.402823466E+38,
            self::DOUBLE => 1.7976931348623157E+308,
            self::BOOLEAN => 1,
            self::CHAR => 255,
            self::VARCHAR => 65535,
            self::TINYTEXT => 255,
            self::TEXT => 65535,
            self::MEDIUMTEXT => 16777215,
            self::LONGTEXT => 4294967295,
            self::ENUM => 65535,
            self::DATE => '9999-12-31',
            self::DATETIME => '9999-12-31 23:59:59',
            self::TIMESTAMP => '2038-01-19 03:14:07',
            self::TIME => '838:59:59',
            self::YEAR => 9999,
        };
    }
}