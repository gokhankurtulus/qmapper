<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:23
 */


namespace QMapper\Core;

use QMapper\Interfaces\IConnection;

abstract class Connection implements IConnection
{
    abstract public function initialize(): void;

    abstract public function terminate(): void;

    protected ?string $indexKey = null;
    protected array $query = [];
    protected array $bindings = [];

    protected ?string $operation = null;
    protected array $cudOperations = ['create', 'update', 'delete'];

    /**
     * @return string|null
     */
    public function getIndexKey(): ?string
    {
        return $this->indexKey;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function setIndexKey(string $column): static
    {
        $this->indexKey = $column;
        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     * @return void
     */
    public function setQuery(array $query): void
    {
        $this->query = $query;
    }

    /**
     * @param mixed $query
     * @return void
     */
    public function addToQuery(mixed $query): void
    {
        $this->query[] = $query;
    }

    /**
     * @return void
     */
    public function clearQuery(): void
    {
        $this->setQuery([]);
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @param array $bindings
     * @return void
     */
    public function setBindings(array $bindings): void
    {
        $this->bindings = $bindings;
    }

    /**
     * @param mixed $binding
     * @return void
     */
    public function addToBindings(mixed $binding): void
    {
        $this->bindings[] = $binding;
    }

    /**
     * @return void
     */
    public function clearBindings(): void
    {
        $this->setBindings([]);
    }

    /**
     * @return string|null
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * @param string|null $operation
     */
    public function setOperation(?string $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * @return array
     */
    public function getCudOperations(): array
    {
        return $this->cudOperations;
    }

    /**
     * @param array|string[] $cudOperations
     */
    public function setCudOperations(array $cudOperations): void
    {
        $this->cudOperations = $cudOperations;
    }
}