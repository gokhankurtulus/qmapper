<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:41
 */


namespace QMapper\Interfaces;

use QMapper\Core\Collection;

interface IBuilder
{
    public function getIndex(): ?string;

    public function setIndex(string $column): self;

    public function getQuery(): mixed;

    public function setQuery(mixed $query): void;

    public function addToQuery(mixed $query): void;

    public function clearQuery(): void;

    public function getBindings(): array;

    public function setBindings(array $bindings);

    public function addToBindings(mixed $binding): void;

    public function clearBindings(): void;

    public function count(): self;

    public function select(array $fields = ['*']): self;

    public function from(string $table): self;

    public function where(array ...$args): self;

    public function orWhere(array ...$args): self;

    public function position(string $logicalOperator, array ...$args): void;

    public function orderBy(array ...$args): self;

    public function limit(?int $limit, ?int $offset = 0): self;

    public function insert(string $table): self;

    public function values(array $values): self;

    public function update(string $table): self;

    public function set(array $values): self;

    public function delete(): self;

    public function build(): Collection;
}