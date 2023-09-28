<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 03:50
 */

namespace QMapper\Enums;
enum DataType
{
    case TINYINT;
    case SMALLINT;
    case MEDIUMINT;
    case INT;
    case BIGINT;

    case DECIMAL;
    case FLOAT;
    case DOUBLE;

    case BOOLEAN;

    case CHAR;
    case VARCHAR;
    case TINYTEXT;
    case TEXT;
    case MEDIUMTEXT;
    case LONGTEXT;

    case ENUM;

    case DATE;
    case DATETIME;
    case TIMESTAMP;
    case TIME;
    case YEAR;

    case UUID;
    case EMAIL;
    case IP;
    case IPV4;
    case IPV6;

    /**
     * Returns min lenght of selected data type
     * @return int|float|string
     */
    public function min(): int|float|string
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
            self::TIMESTAMP => '1970-01-01 00:00:00',
            self::TIME => '-838:59:59',
            self::YEAR => 1901,
            self::UUID => 0,
            self::EMAIL => 0,
            self::IP => 0,
            self::IPV4 => 0,
            self::IPV6 => 0,
        };
    }

    /**
     * Returns max lenght of selected data type
     * @return int|float|string
     */
    public function max(): int|float|string
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
            self::TIMESTAMP => '2037-01-01 00:00:00',
            self::TIME => '838:59:59',
            self::YEAR => 2155,
            self::UUID => 36,
            self::EMAIL => 320,
            self::IP => 45,
            self::IPV4 => 15,
            self::IPV6 => 45,
        };
    }
}