<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 12.07.2023 Time: 03:40
 */


namespace QMapper\Traits\Field;

use QMapper\Enums\DataType;

trait FieldDataTypeHelper
{
    public function varchar(string $name): static
    {
        $this->name($name)
            ->type(DataType::VARCHAR);
        return $this;
    }

    public function text(string $name): static
    {
        $this->name($name)
            ->type(DataType::TEXT);
        return $this;
    }

    public function smallint(string $name): static
    {
        $this->name($name)
            ->type(DataType::SMALLINT);
        return $this;
    }

    public function int(string $name): static
    {
        $this->name($name)
            ->type(DataType::INT);
        return $this;
    }

    public function bigint(string $name): static
    {
        $this->name($name)
            ->type(DataType::BIGINT);
        return $this;
    }

    public function boolean(string $name): static
    {
        $this->name($name)
            ->type(DataType::BOOLEAN);
        return $this;
    }

    public function enum(string $name): static
    {
        $this->name($name)
            ->type(DataType::ENUM);
        return $this;
    }

    public function date(string $name): static
    {
        $this->name($name)
            ->type(DataType::DATE);
        return $this;
    }

    public function time(string $name): static
    {
        $this->name($name)
            ->type(DataType::TIME);
        return $this;
    }

    public function timestamp(string $name): static
    {
        $this->name($name)
            ->type(DataType::TIMESTAMP);
        return $this;
    }
}