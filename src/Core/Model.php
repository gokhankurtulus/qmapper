<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:20
 */


namespace QMapper\Core;

use JsonSerializable;
use QMapper\Interfaces\Arrayable;
use QMapper\Interfaces\Jsonable;
use QMapper\Exceptions\ModelException;
use QMapper\Traits\Attributes;
use QMapper\Traits\Interactions;

abstract class Model extends Builder implements Arrayable, Jsonable, JsonSerializable
{
    use Attributes, Interactions;

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     * @return string
     * @throws ModelException
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ModelException(sprintf("Error encoding model %s to JSON: %s", basename($this::class), json_last_error_msg()));
        }

        return $json;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}