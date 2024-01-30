<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:25
 */

namespace QMapper\Interfaces;

use QMapper\Core\Collections\DatabaseResultCollection;

interface IBuilder
{
    public function getComparisonOperator(string $operator): string;

    public function getOperation(): ?string;

    public function setOperation(?string $operation): void;

    public function count(array $fields = ['*']): static;

    public function select(array $fields = ['*']): static;

    public function from(string $table): static;

    public function as(string $as): static;

    public function innerJoin(string $table, string $on): static;

    public function leftJoin(string $table, string $on): static;

    public function rightJoin(string $table, string $on): static;

    public function fullJoin(string $table, string $on): static;

    public function where(array ...$args): static;

    public function orWhere(array ...$args): static;

    public function position(string $logicalOperator, array ...$args): void;

    public function orderBy(array ...$args): static;

    public function limit(?int $limit, ?int $offset = 0): static;

    public function insert(string $table): static;

    public function values(array $values): static;

    public function update(string $table): static;

    public function set(array $values): static;

    public function delete(): static;

    public function build(): DatabaseResultCollection;
}