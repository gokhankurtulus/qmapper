<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:41
 */


namespace QMapper\Interfaces;

interface BuilderInterface
{
    public function getQuery(): string;

    public function setQuery(string $query);

    public function addToQuery(string $query);

    public function clearQuery();

    public function getBindings(): array;

    public function setBindings(array $bindings);

    public function addToBindings(mixed $binding);

    public function clearBindings();

    public function count(): self;

    public function select(array $fields = ['*']): self;

    public function from(string $table): self;

    public function where(string $operatorIfHasMultiple = 'AND', array ...$args): self;

    public function orWhere(string $operatorIfHasMultiple = 'AND', array ...$args): self;

    public function position(string $logicalOperator, string $operatorIfHasMultiple = 'AND', array ...$args);

    public function orderBy(array ...$args): self;

    public function limit(?int $limit): self;

    public function offset(?int $offset): self;

    public function insert(string $table): self;

    public function values(array $values): self;

    public function update(string $table): self;

    public function set(array $values): self;

    public function delete(): self;

    public function build();
}