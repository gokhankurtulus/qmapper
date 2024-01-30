<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:25
 */


namespace QMapper\Interfaces;

interface IConnection
{
    public function initialize(): void;

    public function terminate(): void;

    public function getIndexKey(): ?string;

    public function setIndexKey(string $column): static;

    public function getQuery(): array;

    public function setQuery(array $query): void;

    public function addToQuery(mixed $query): void;

    public function clearQuery(): void;

    public function getBindings(): array;

    public function setBindings(array $bindings);

    public function addToBindings(mixed $binding): void;

    public function clearBindings(): void;

    public function getCudOperations(): array;

    public function setCudOperations(array $cudOperations): void;
}