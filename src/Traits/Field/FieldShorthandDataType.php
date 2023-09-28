<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 21.07.2023 Time: 00:47
 */


namespace QMapper\Traits\Field;

use QMapper\Enums\DataType;

trait FieldShorthandDataType
{
    public function index(?string $name = null): static
    {
        $this->id($name)
            ->increament()
            ->primary()
            ->identifier();
        return $this;
    }

    public function id(?string $name = null): static
    {
        $this->bigint($name ?? 'id');
        return $this;
    }

    public function string(string $name): static
    {
        $this->text($name);
        return $this;
    }

    public function uuid(?string $name = null): static
    {
        $this->name($name ?? 'uuid')
            ->type(DataType::UUID)
            ->length([36])
            ->unique()
            ->identifier()
            ->searchable();
        return $this;
    }

    public function email(string $name): static
    {
        $this->name($name)
            ->type(DataType::EMAIL)
            ->length([320]);
        return $this;
    }

    public function ip(string $name): static
    {
        $this->name($name)
            ->type(DataType::IP)
            ->length([45]);
        return $this;
    }

    public function ipv4(string $name): static
    {
        $this->name($name)
            ->type(DataType::IPV4)
            ->length([15]);
        return $this;
    }

    public function ipv6(string $name): static
    {
        $this->name($name)
            ->type(DataType::IPV6)
            ->length([45]);
        return $this;
    }

    public function createdAt(?string $name = null): static
    {
        $this->timestamp($name ?? 'created_at')
            ->default('CURRENT_TIMESTAMP');
        return $this;
    }

    public function updatedAt(?string $name = null): static
    {
        $this->timestamp($name ?? 'updated_at')
            ->default('CURRENT_TIMESTAMP')
            ->extra('on update CURRENT_TIMESTAMP');
        return $this;
    }

    /**
     * @return bool
     */
    public function isUuid(): bool
    {
        return $this->getType() === DataType::UUID;
    }

    public function isEmail(): bool
    {
        return $this->getType() === DataType::EMAIL;
    }

    public function isIP(): bool
    {
        return $this->getType() === DataType::IP || $this->isIPv4() || $this->isIPv6();
    }

    public function isIPv4(): bool
    {
        return $this->getType() === DataType::IPV4;
    }

    public function isIPv6(): bool
    {
        return $this->getType() === DataType::IPV6;
    }
}