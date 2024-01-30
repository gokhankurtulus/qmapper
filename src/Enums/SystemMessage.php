<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 18:08
 */

namespace QMapper\Enums;

enum SystemMessage
{
    case SPECIFIC;
    case EXTENSION_REQUIRED;
    case DOES_NOT_EXIST;
    case IS_NOT_ALLOWED;
    case FAILED_TO;
    case INVALID_ARGUMENTS;
    case MISSING;

    public function get(string ...$bindings): string
    {
        $message = match ($this) {
            self::SPECIFIC => "%s",
            self::EXTENSION_REQUIRED => "%s extension is required or is not loaded correctly.",
            self::DOES_NOT_EXIST => "%s doesn't exist.",
            self::IS_NOT_ALLOWED => "%s isn't allowed.",
            self::FAILED_TO => "Failed to %s.",
            self::INVALID_ARGUMENTS => "Invalid arguments %s.",
            self::MISSING => "%s missing.",
        };
        return vsprintf($message, $bindings) ?? "";
    }
}
