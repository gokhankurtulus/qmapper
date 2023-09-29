<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 31.03.2023 Time: 02:19
 */


namespace QMapper\Enums;


enum MapperStringTemplate
{
    case EXTENSION_REQUIRED;
    case INVALID_ARGUMENTS;
    case DATA_ISNOT_SENT_CORRECT;
    case NOT_FOUND;
    case NOT_FOUND_IN;

    case CANT_GIVEN_WHILE_CREATION;

    case FIELD_DOESNT_EXIST;
    case FIELD_IS_REQUIRED;
    case FIELD_CANT_BE_NULL;

    case FIELD_MUST_BE_UNIQUE;
    case FIELD_MUST_BE_TYPE;
    case FIELD_MUST_BE_SAME;
    case FIELD_MUST_BE_ONE_OF;
    case FIELD_MUST_CONTAIN_CHAR_PATTERN;

    case FIELD_UNIQUE_CANT_UPDATE;
    case FIELD_NOT_VALID_EMAIL;
    case FIELD_NOT_VALID_IP;

    case FIELD_MIN_LENGHT_CAN_BE;
    case FIELD_MAX_LENGHT_CAN_BE;
    case FIELD_MIN_VALUE_CAN_BE;
    case FIELD_MAX_VALUE_CAN_BE;

    case MODEL_DOESNT_HAVE;

    /**
     * Returns string messages with included args
     * @param string ...$args
     * @return string|null
     */
    public function get(string ...$args): ?string
    {
        $message = match ($this) {
            self::EXTENSION_REQUIRED => "%s extension is required or is not loaded correctly.",
            self::INVALID_ARGUMENTS => "Invalid Arguments",
            self::DATA_ISNOT_SENT_CORRECT => "Data is not sent in correct format.",
            self::NOT_FOUND => "%s not found.",
            self::NOT_FOUND_IN => "%s not found in %s.",
            self::CANT_GIVEN_WHILE_CREATION => "%s field cannot be given during %s creation.",
            self::FIELD_DOESNT_EXIST => "%s field doesn't exist in %s.",
            self::FIELD_IS_REQUIRED => "%s field is required.",
            self::FIELD_CANT_BE_NULL => "%s cannot be null.",
            self::FIELD_MUST_BE_UNIQUE => "%s field must be unique.",
            self::FIELD_MUST_BE_TYPE => "%s field must be %s data type.",
            self::FIELD_MUST_BE_SAME => "%s field must be the same as %s.",
            self::FIELD_MUST_BE_ONE_OF => "%s field must be one of %s.",
            self::FIELD_MUST_CONTAIN_CHAR_PATTERN => "%s field must only contain characters that match the pattern; %s.",
            self::FIELD_UNIQUE_CANT_UPDATE => "Batch update cannot be performed because data contains the %s field, which must be unique.",
            self::FIELD_NOT_VALID_EMAIL => "%s field must contain valid email address.",
            self::FIELD_NOT_VALID_IP => "%s field must contain valid IP address.",
            self::FIELD_MIN_LENGHT_CAN_BE => "The minimum length of the %s field can set to at least %s.",
            self::FIELD_MAX_LENGHT_CAN_BE => "The maximum length of the %s field can set to at most %s.",
            self::FIELD_MIN_VALUE_CAN_BE => "The minimum length of the %s field can be %s.",
            self::FIELD_MAX_VALUE_CAN_BE => "The maximum length of the %s field can be %s.",
            self::MODEL_DOESNT_HAVE => "The model %s doesn't have %s.",
            default => "",
        };
        return vsprintf($message, $args) ?? null;
    }
}