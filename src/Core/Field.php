<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 12.07.2023 Time: 02:12
 */


namespace QMapper\Core;

use QMapper\Enums\DataType;
use QMapper\Traits\Field\FieldDataTypeHelper;
use QMapper\Traits\Field\FieldShorthandDataType;
use QMapper\Traits\Field\FieldTableHelper;
use QMapper\Traits\Field\FieldValidation;

class Field
{
    use FieldDataTypeHelper;
    use FieldShorthandDataType;
    use FieldValidation;
    use FieldTableHelper;

    private ?string $name = null;
    private ?DataType $type = null;
    private mixed $value = null;
    private array $length = [];

    private int|float|string|null $min = null;
    private int|float|string|null $max = null;


    /**
     * Get the variable's name.
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the variable's name.
     * @param string $name
     * @return $this
     */
    protected function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the variable's type.
     * @return DataType|null
     */
    public function getType(): ?DataType
    {
        return $this->type;
    }

    /**
     * Set the variable's type.
     * @param DataType $type
     * @return $this
     */
    protected function type(DataType $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the variable's value.
     * @return mixed|null
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Set the variable's value.
     * @param mixed|null $value
     * @return $this
     */
    public function value(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the variable's length.
     * @return array
     */
    public function getLength(): array
    {
        return $this->length;
    }

    /**
     * Set the variable's length.
     * @param array $length
     * @return $this
     */
    public function length(array $length): static
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return float|int|string|null
     */
    public function getMin(): float|int|string|null
    {
        return $this->min;
    }

    /**
     * @param float|int|string $min
     * @return $this
     */
    public function min(float|int|string $min): static
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return float|int|string|null
     */
    public function getMax(): float|int|string|null
    {
        return $this->max;
    }

    /**
     * @param float|int|string $max
     * @return $this
     */
    public function max(float|int|string $max): static
    {
        $this->max = $max;
        return $this;
    }
}