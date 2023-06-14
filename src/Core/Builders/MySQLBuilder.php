<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:38
 */


namespace QMapper\Core\Builders;

use QMapper\Core\Connections\PDOConnection;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\BuilderInterface;

class MySQLBuilder extends PDOConnection implements BuilderInterface
{

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query)
    {
        $this->query = $query;
    }

    public function addToQuery(string $query)
    {
        $this->query .= " $query";
    }

    public function clearQuery()
    {
        $this->setQuery("");
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function setBindings(array $bindings)
    {
        $this->bindings = $bindings;
    }

    public function addToBindings(mixed $binding)
    {
        $this->bindings[] = $binding;
    }

    public function clearBindings()
    {
        $this->setBindings([]);
    }

    public function count(): self
    {
        $this->select(['count(*)']);
        return $this;
    }

    public function select(array $fields = ['*']): self
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("SELECT " . implode(', ', $fields));
        return $this;
    }

    public function from(string $table): self
    {
        $this->addToQuery("FROM $table");
        return $this;
    }

    public function where(string $operatorIfHasMultiple = 'AND', array ...$args): self
    {
        $this->position("AND", $operatorIfHasMultiple, ...$args);
        return $this;
    }

    public function orWhere(string $operatorIfHasMultiple = 'AND', array ...$args): self
    {
        $this->position("OR", $operatorIfHasMultiple, ...$args);
        return $this;
    }

    public function position(string $logicalOperator, string $operatorIfHasMultiple = 'AND', array ...$args)
    {
        if (empty($args) || (count($args) === 1 && empty($args[0])))
            return;
        $clause = "";

        if (count($args) > 1) {
            $clause .= "(";
        }

        foreach ($args as $index => $condition) {
            if (count($condition) !== 3)
                throw new DatabaseException('Invalid arguments.');
            [$field, $operator, $value] = $condition;
            if ($index > 0) {
                $clause .= " {$operatorIfHasMultiple} ";
            }
            $clause .= "{$field} {$operator} ?";
            $this->addToBindings($value);
        }
        if (count($args) > 1) {
            $clause .= ")";
        }
        $this->addToQuery((!str_contains($this->getQuery(), "WHERE") ? " WHERE " : " {$logicalOperator} ") . $clause);
    }

    public function orderBy(array ...$args): self
    {
        $clause = '';
        $multipleOrderClauses = count($args) > 1;
        $lastItem = array_key_last($args);
        foreach ($args as $index => $sort) {
            if (count($sort) !== 1 && count($sort) !== 2) {
                throw new DatabaseException('Invalid arguments.');
            }
            @[$field, $direction] = $sort;
            $clause .= " {$field} {$direction} " . ($multipleOrderClauses && $index !== $lastItem ? ',' : '');
        }
        $this->addToQuery(!str_contains($this->getQuery(), "ORDER BY") ? " ORDER BY $clause " : " $clause ");
        return $this;
    }

    public function limit(?int $limit): self
    {
        if (!is_null($limit))
            $this->addToQuery(" LIMIT $limit");
        return $this;
    }

    public function offset(?int $offset): self
    {
        if (!is_null($offset))
            $this->addToQuery(" OFFSET $offset");
        return $this;
    }

    public function insert(string $table): self
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("INSERT INTO $table ");
        return $this;
    }

    public function values(array $values): self
    {
        $fields = array_keys($values);
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->setBindings(array_values($fields));
        $this->addToQuery("(" . implode(', ', $fields) . ") VALUES ($placeholders)");
        return $this;
    }

    public function update(string $table): self
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("UPDATE $table ");
        return $this;
    }

    public function set(array $values): self
    {
        $set = [];
        foreach ($values as $field => $value) {
            $this->addToBindings($value);
            $set[] = "$field = ?";
        }
        $this->setQuery("SET " . implode(', ', $set) . " ");
        return $this;
    }

    public function delete(): self
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery("DELETE ");
        return $this;
    }


    public function build()
    {
        echo $this->getQuery();
        try {
            $this->getPDO()->beginTransaction();
            $statement = $this->getPDO()->prepare($this->getQuery());
            $statement->execute($this->getBindings());
            if ($this->getPDO()->inTransaction())
                $this->getPDO()->commit();
            return [
                'data' => $statement->fetchAll(),
                'row_count' => $statement->rowCount(),
                'last_insert_id' => $this->getPDO()->lastInsertId(),
            ];
        } catch (\PDOException $ex) {
            throw new DatabaseException($ex->getMessage());
        }
    }
}