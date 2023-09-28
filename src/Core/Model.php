<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:20
 */


namespace QMapper\Core;

use QMapper\Exceptions\AttributeException;
use QMapper\Exceptions\ModelException;
use QMapper\Interfaces\Arrayable;
use QMapper\Interfaces\Jsonable;
use QMapper\Traits\Model\Attributes;
use QMapper\Traits\Model\Interactions;

abstract class Model extends Builder implements Arrayable, Jsonable
{
    use Attributes;
    use Interactions;

    /**
     * @return Field[]
     */
    abstract protected static function schema(): array;

    /**
     * @param array $fields
     * @param bool $raiseError
     * @return bool
     * @throws AttributeException
     */
    public function validate(array $fields, bool $raiseError = false): bool
    {
        try {
            return (new Validator($this, $fields))->validate();
        } catch (\Exception $exception) {
            if ($raiseError)
                throw $exception;
            return false;
        }
    }

    /**
     * Returns true if instance has different values from given fields
     * @param array $fields
     * @return bool
     */
    public function hasDiff(array $fields): bool
    {
        try {
            return (new Validator(static::getInstance(), $fields))->hasDiff();
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Returns true if instance has index value
     * @return bool
     */
    public function hasIndex(): bool
    {
        return (bool)$this->getProperty(static::getIndexKey());
    }

    /**
     * Returns true if instance has properties
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->getProperties());
    }

    /**
     * Remove hidden fields from properties
     * @return array
     */
    public function toArray(): array
    {
        return array_diff_key($this->getProperties(), array_flip($this->getHiddens()));
    }

    /**
     * @param int $options
     * @return string
     * @throws ModelException
     */
    public function toJson(int $options = JSON_FORCE_OBJECT): string
    {
        $json = json_encode($this->toArray(), $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ModelException(sprintf("Error encoding model %s to JSON: %s", basename($this::class), json_last_error_msg()));
        }
        return $json;
    }
}