<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:33
 */

namespace QMapper\Enums;

use QMapper\Interfaces\IBuilder;
use QMapper\Core\Builders\MySQLBuilder;

enum DatabaseDriver: string
{
    case MySQL = 'mysql';

    public function getBuilder(): IBuilder
    {
        return match ($this) {
            self::MySQL => new MySQLBuilder(),
        };
    }
}
