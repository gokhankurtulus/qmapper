<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 04:55
 */


namespace QMapper\Traits;

trait NullSupportedEnum
{
    /**
     * @param int|string|null $value
     * @return static|null
     */
    public static function tryFromNotNull(int|string|null $value): ?static
    {
        if ($value === null) {
            return null;
        }
        return self::tryFrom($value);
    }
}