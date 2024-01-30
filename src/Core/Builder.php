<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:23
 */


namespace QMapper\Core;

use QMapper\Interfaces\IBuilder;
use QMapper\Core\Collections\DatabaseResultCollection;

class Builder
{
    private ?IBuilder $builder;

    public function __construct(IBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function initialize(): void
    {
        $this->builder?->initialize();
    }

    public function terminate(): void
    {
        $this->builder?->terminate();
    }

    public function getIndex(): ?string
    {
        return $this->builder?->getIndexKey();
    }

    public function setIndex(string $column): static
    {
        $this->builder?->setIndexKey($column);
        return $this;
    }

    public function getQuery(): mixed
    {
        return $this->builder?->getQuery();
    }

    public function setQuery(mixed $query): void
    {
        $this->builder?->setQuery($query);
    }

    public function addToQuery(mixed $query): void
    {
        $this->builder?->addToQuery($query);
    }

    public function clearQuery(): void
    {
        $this->builder?->clearQuery();
    }

    public function getBindings(): array
    {
        return $this->builder?->getBindings();
    }

    public function setBindings(array $bindings): void
    {
        $this->builder?->setBindings($bindings);
    }

    public function clearBindings(): void
    {
        $this->builder?->clearBindings();
    }

    public function getOperation(): ?string
    {
        return $this->builder?->getOperation();
    }

    public function setOperation(?string $operation): void
    {
        $this->builder?->setOperation($operation);
    }

    public function getCudOperations(): array
    {
        return $this->builder?->getCudOperations();
    }

    public function setCudOperations(array $cudOperations): void
    {
        $this->builder?->setCudOperations($cudOperations);
    }

    public function count(array $fields = ['*']): static
    {
        $this->builder?->count($fields);
        return $this;
    }

    public function select(array $fields = ['*']): static
    {
        $this->builder?->select($fields);
        return $this;
    }

    public function from(string $table): static
    {
        $this->builder?->from($table);
        return $this;
    }

    public function as(string $as): static
    {
        $this->builder?->as($as);
        return $this;
    }

    public function innerJoin(string $table, string $on): static
    {
        $this->builder?->innerJoin($table, $on);
        return $this;
    }

    public function leftJoin(string $table, string $on): static
    {
        $this->builder?->leftJoin($table, $on);
        return $this;
    }

    public function rightJoin(string $table, string $on): static
    {
        $this->builder?->rightJoin($table, $on);
        return $this;
    }

    public function fullJoin(string $table, string $on): static
    {
        $this->builder?->fullJoin($table, $on);
        return $this;
    }

    public function where(array ...$args): static
    {
        $this->builder?->where(...$args);
        return $this;
    }

    public function orWhere(array ...$args): static
    {
        $this->builder?->orWhere(...$args);
        return $this;
    }

    public function orderBy(array ...$args): static
    {
        $this->builder?->orderBy(...$args);
        return $this;
    }

    public function limit(?int $limit, ?int $offset = 0): static
    {
        $this->builder?->limit($limit, $offset);
        return $this;
    }

    public function insert(string $table): static
    {
        $this->builder?->insert($table);
        return $this;
    }

    public function values(array $values): static
    {
        $this->builder?->values($values);
        return $this;
    }

    public function update(string $table): static
    {
        $this->builder?->update($table);
        return $this;
    }

    public function set(array $values): static
    {
        $this->builder?->set($values);
        return $this;
    }

    public function delete(): static
    {
        $this->builder?->delete();
        return $this;
    }

    public function build(): DatabaseResultCollection
    {
        return $this->builder?->build();
    }
}