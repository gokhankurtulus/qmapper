<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:38
 */


namespace QMapper\Core\Builders;

use QMapper\Core\Collection;
use QMapper\Core\Connections\PDOConnection;
use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\BuilderException;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IBuilder;

abstract class PDOBuilder extends PDOConnection implements IBuilder
{
    abstract public function initialize(): void;

    protected ?string $index = null;
    protected mixed $query = null;
    protected array $bindings = [];

    protected ?string $operation = null;
    protected array $cudOperations = ['create', 'update', 'delete'];

    /**
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function setIndex(string $column): self
    {
        $this->index = $column;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery(): mixed
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     * @return void
     */
    public function setQuery(mixed $query): void
    {
        $this->query = $query;
    }

    /**
     * @param mixed $query
     * @return void
     */
    public function addToQuery(mixed $query): void
    {
        $this->query .= " $query";
    }

    /**
     * @return void
     */
    public function clearQuery(): void
    {
        $this->setQuery("");
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

    /**
     * @return $this
     */
    public function count(): self
    {
        $this->select(['count(*)']);
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function select(array $fields = ['*']): self
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("SELECT " . implode(', ', $fields));
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from(string $table): self
    {
        $this->addToQuery("FROM {$table}");
        return $this;
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws BuilderException
     */
    public function where(array ...$args): self
    {
        $this->position("AND", ...$args);
        return $this;
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws BuilderException
     */
    public function orWhere(array ...$args): self
    {
        $this->position("OR", ...$args);
        return $this;
    }

    /**
     * @param string $logicalOperator
     * @param array ...$args
     * @return void
     * @throws BuilderException
     */
    public function position(string $logicalOperator, array ...$args): void
    {
        if (empty($args) || (count($args) === 1 && empty($args[0])))
            return;
        $clause = "";

        if (count($args) > 1) {
            $clause .= "(";
        }

        foreach ($args as $index => $condition) {
            if (count($condition) !== 3)
                throw new BuilderException(MapperStringTemplate::INVALID_ARGUMENTS->get());
            [$field, $operator, $value] = $condition;
            if ($index > 0) {
                $clause .= " AND ";
            }
            $clause .= "{$field} {$this->getCompareOperator($operator)} ?";
            $this->addToBindings($value);
        }
        if (count($args) > 1) {
            $clause .= ")";
        }
        $this->addToQuery((!str_contains($this->getQuery(), "WHERE") ? " WHERE " : " {$logicalOperator} ") . "{$clause}");
    }

    /**
     * @param string $operator
     * @return string
     */
    private function getCompareOperator(string $operator): string
    {
        $operatorMap = [
            '=' => '=',
            '!=' => '!=',
            '>' => '>',
            '>=' => '>=',
            '<' => '<',
            '<=' => '<=',
        ];

        return $operatorMap[$operator] ?? '=';
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws BuilderException
     */
    public function orderBy(array ...$args): self
    {
        $clause = '';
        $multipleOrderClauses = count($args) > 1;
        $lastItem = array_key_last($args);
        foreach ($args as $index => $sort) {
            if (count($sort) !== 1 && count($sort) !== 2) {
                throw new BuilderException(MapperStringTemplate::INVALID_ARGUMENTS->get());
            }
            @[$field, $direction] = $sort;
            $clause .= " {$field} {$direction} " . ($multipleOrderClauses && $index !== $lastItem ? ',' : '');
        }
        $this->addToQuery(!str_contains($this->getQuery(), "ORDER BY") ? " ORDER BY {$clause} " : " {$clause} ");
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return $this
     */
    public function limit(?int $limit, ?int $offset = 0): self
    {
        if (!is_null($limit))
            $this->addToQuery(" LIMIT {$limit}");
        if (!is_null($offset))
            $this->addToQuery(" OFFSET {$offset}");
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function insert(string $table): self
    {
        $this->setOperation('create');
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("INSERT INTO {$table} ");
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function values(array $values): self
    {
        $fields = array_keys($values);
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->setBindings(array_values($values));
        $this->addToQuery("(" . implode(', ', $fields) . ") VALUES ($placeholders)");
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function update(string $table): self
    {
        $this->setOperation('update');
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("UPDATE {$table} ");
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function set(array $values): self
    {
        $set = [];
        foreach ($values as $field => $value) {
            $this->addToBindings($value);
            $set[] = "{$field} = ?";
        }
        $this->addToQuery("SET " . implode(', ', $set) . " ");
        return $this;
    }

    /**
     * @return $this
     */
    public function delete(): self
    {
        $this->setOperation('delete');
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("DELETE ");
        return $this;
    }


    /**
     * @return Collection
     * @throws BuilderException
     */
    public function build(): Collection
    {
        try {
            $this->getPDO()->beginTransaction();
            $statement = $this->getPDO()->prepare($this->getQuery());
            $statement->execute($this->getBindings());
            $data = $statement->fetchAll();
            $rowCount = $statement->rowCount();
            $lastInsertId = $this->getPDO()->lastInsertId();
            if ($this->getPDO()->inTransaction())
                $this->getPDO()->commit();
            return new Collection($data, $rowCount, $lastInsertId);
        } catch (\Exception|\Throwable $ex) {
            if ($this->getPDO()->inTransaction())
                $this->getPDO()->rollBack();
            throw new BuilderException($ex->getMessage());
        }
    }
}