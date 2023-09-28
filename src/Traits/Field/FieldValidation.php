<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 23.07.2023 Time: 03:48
 */


namespace QMapper\Traits\Field;


use DateTime;
use QMapper\Enums\DataType;
use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\AttributeException;

trait FieldValidation
{
    /**
     * @throws AttributeException
     */
    public function validate(mixed $value, array $attributes): bool
    {
        $minVal = $this->getMin() ?? $this->getType()->min();
        $maxVal = $this->getMax() ?? $this->getType()->max();
        if (!$this->isNullable() && strlen(preg_replace('/\s+/', '', $value)) === 0)
            throw new AttributeException(MapperStringTemplate::FIELD_CANT_BE_NULL->get($this->getName()));
        if (!is_null($value)) {
            if ($this->getType() === DataType::OBJECTID)
                $this->validateObjectId($value, $minVal, $maxVal);
            elseif ($this->getType() === DataType::TINYINT ||
                $this->getType() === DataType::SMALLINT ||
                $this->getType() === DataType::MEDIUMINT ||
                $this->getType() === DataType::INT ||
                $this->getType() === DataType::BIGINT ||
                $this->getType() === DataType::DECIMAL ||
                $this->getType() === DataType::FLOAT ||
                $this->getType() === DataType::DOUBLE ||
                $this->getType() === DataType::YEAR)
                $this->validateNumber($value, $minVal, $maxVal);
            elseif ($this->getType() === DataType::BOOLEAN)
                $this->validateBoolean($value, $minVal, $maxVal);
            elseif ($this->getType() === DataType::CHAR ||
                $this->getType() === DataType::VARCHAR ||
                $this->getType() === DataType::TINYTEXT ||
                $this->getType() === DataType::TEXT ||
                $this->getType() === DataType::MEDIUMTEXT ||
                $this->getType() === DataType::LONGTEXT)
                $this->validateString($value, $minVal, $maxVal);
            elseif ($this->getType() === DataType::ENUM)
                $this->validateEnum($value, $minVal, $maxVal);
            elseif ($this->getType() === DataType::DATE || $this->getType() === DataType::DATETIME || $this->getType() === DataType::TIMESTAMP)
                $this->validateDate($value, $minVal, $maxVal);
        }
        if ($this->getMatch()) {
            $matched = false;
            foreach ($attributes as $attribute)
                if ($this->getMatch() === $attribute->getName()) {
                    if ($value !== $attribute->getValue())
                        throw new AttributeException(MapperStringTemplate::FIELD_MUST_BE_SAME->get($this->getName(), $attribute->getName()));
                    $matched = true;
                    break;
                }
            if (!$matched)
                throw new AttributeException(MapperStringTemplate::NOT_FOUND_IN->get($this->getMatch(), basename(static::class)));
        }
        return true;
    }

    /**
     * @param mixed $value
     * @param float|int|string $minVal
     * @param float|int|string $maxVal
     * @return bool
     * @throws AttributeException
     */
    public function validateObjectId(mixed $value, float|int|string $minVal, float|int|string $maxVal): bool
    {
        if (!is_string($value) || !ctype_xdigit($value))
            throw new AttributeException(MapperStringTemplate::FIELD_MUST_BE_TYPE->get($this->getName(), $this->getType()->name));
        if (strlen($value) < $minVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MIN_VALUE_CAN_BE->get($this->getName(), $minVal));
        if (strlen($value) > $maxVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MAX_VALUE_CAN_BE->get($this->getName(), $maxVal));
        return true;
    }

    /**
     * @param $value
     * @param $minVal
     * @param $maxVal
     * @return bool
     * @throws AttributeException
     */
    public function validateNumber($value, $minVal, $maxVal): bool
    {
        if (!is_numeric($value))
            throw new AttributeException(MapperStringTemplate::FIELD_MUST_BE_TYPE->get($this->getName(), $this->getType()->name));
        if ($value < $minVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MIN_VALUE_CAN_BE->get($this->getName(), $minVal));
        if ($value > $maxVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MAX_VALUE_CAN_BE->get($this->getName(), $maxVal));
        return true;
    }

    /**
     * @param $value
     * @param $minVal
     * @param $maxVal
     * @return bool
     * @throws AttributeException
     */
    public function validateBoolean($value, $minVal, $maxVal): bool
    {
        if ($value !== true && $value !== false && intval($value) !== $minVal && intval($value) !== $maxVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MUST_BE_TYPE->get($this->getName(), $this->getType()->name));
        return true;
    }

    /**
     * @param $value
     * @param $minVal
     * @param $maxVal
     * @return bool
     * @throws AttributeException
     */
    public function validateString($value, $minVal, $maxVal): bool
    {
        if (!is_string($value))
            throw new AttributeException(MapperStringTemplate::FIELD_MUST_BE_TYPE->get($this->getName(), $this->getType()->name));
        if (strlen($value) < $minVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MIN_VALUE_CAN_BE->get($this->getName(), $minVal));
        if (strlen($value) > $maxVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MAX_VALUE_CAN_BE->get($this->getName(), $maxVal));
        if (!is_null($this->getRegex()) && !preg_match("/^{$this->getRegex()}*$/", $value))
            throw new AttributeException(MapperStringTemplate::FIELD_MUST_CONTAIN_CHAR_PATTERN->get($this->getName(), $this->getRegex()));
        if ($this->isEmail() && !filter_var($value, FILTER_VALIDATE_EMAIL))
            throw new AttributeException(MapperStringTemplate::FIELD_NOT_VALID_EMAIL->get($this->getName()));
        if ($this->isIP() && !filter_var($value, FILTER_VALIDATE_IP))
            throw new AttributeException(MapperStringTemplate::FIELD_NOT_VALID_IP->get($this->getName()));
        return true;
    }

    /**
     * @param $value
     * @param $minVal
     * @param $maxVal
     * @return bool
     * @throws AttributeException
     */
    public function validateEnum($value, $minVal, $maxVal): bool
    {
        if (!in_array($value, $this->getLength()))
            throw new AttributeException(MapperStringTemplate::FIELD_MUST_BE_ONE_OF->get($this->getName(), implode(" | ", $this->getLength())));
        if (strlen($value) < $minVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MIN_VALUE_CAN_BE->get($this->getName(), $minVal));
        if (strlen($value) > $maxVal)
            throw new AttributeException(MapperStringTemplate::FIELD_MAX_VALUE_CAN_BE->get($this->getName(), $maxVal));
        return true;
    }

    /**
     * @param $value
     * @param $minVal
     * @param $maxVal
     * @return bool
     * @throws AttributeException
     */
    public function validateDate($value, $minVal, $maxVal): bool
    {
        $givenDate = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if ($givenDate === false)
            throw new AttributeException(MapperStringTemplate::FIELD_MUST_BE_TYPE->get($this->getName(), $this->getType()->name . "(Y-m-d H:i:s)"));
        $minDate = DateTime::createFromFormat('Y-m-d H:i:s', $minVal);
        $maxDate = DateTime::createFromFormat('Y-m-d H:i:s', $maxVal);
        if ($givenDate < $minDate)
            throw new AttributeException(MapperStringTemplate::FIELD_MIN_VALUE_CAN_BE->get($this->getName(), $minVal));
        if ($givenDate > $maxDate)
            throw new AttributeException(MapperStringTemplate::FIELD_MAX_VALUE_CAN_BE->get($this->getName(), $maxVal));
        return true;
    }
}