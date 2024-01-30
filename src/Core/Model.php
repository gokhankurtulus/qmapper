<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:23
 */


namespace QMapper\Core;

use QMapper\Interfaces\Arrayable;
use QMapper\Interfaces\Jsonable;
use QMapper\Traits\Model\ModelInteractions;
use QMapper\Exceptions\ModelException;

class Model implements Arrayable, Jsonable
{
    use ModelInteractions;

    /**
     * Remove hidden fields from properties
     * @return array
     */
    public function toArray(): array
    {
        return $this->getProperties();
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