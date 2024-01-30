<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:21
 */

namespace QMapper\Core\Builders\Base;

use QMapper\Core\Collections\DatabaseResultCollection;
use QMapper\Core\Connections\PDOConnection;
use QMapper\Interfaces\IBuilder;
use QMapper\Enums\SystemMessage;
use QMapper\Exceptions\BuilderException;
use QMapper\Loggers\PDOLogger;

abstract class PDOBuilder extends PDOConnection implements IBuilder
{
    abstract public function initialize(): void;

    public function getComparisonOperator(string $operator): string
    {
        $operatorMap = [
            '=' => '=',
            '!=' => '!=',
            '<>' => '!=',
            '>' => '>',
            '>=' => '>=',
            '<' => '<',
            '<=' => '<=',
        ];

        return $operatorMap[$operator] ?? '=';
    }

    /**
     * @return $this
     */
    public function count(array $fields = ['*']): static
    {
        $this->select(['COUNT(' . implode(', ', $fields) . ')']);
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function select(array $fields = ['*']): static
    {
        $this->setOperation('select');
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery(["SELECT " . implode(', ', $fields)]);
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from(string $table): static
    {
        $this->addToQuery(" FROM {$table}");
        return $this;
    }

    /**
     * @param string $as
     * @return $this
     */
    public function as(string $as): static
    {
        $this->addToQuery(" AS $as ");
        return $this;
    }

    public function innerJoin(string $table, string $on): static
    {
        $this->addToQuery(" INNER JOIN $table ON {$on} ");
        return $this;
    }

    public function leftJoin(string $table, string $on): static
    {
        $this->addToQuery(" LEFT JOIN $table ON {$on} ");
        return $this;
    }

    public function rightJoin(string $table, string $on): static
    {
        $this->addToQuery(" RIGHT JOIN $table ON {$on} ");
        return $this;
    }

    public function fullJoin(string $table, string $on): static
    {
        $this->addToQuery(" FULL OUTER JOIN $table ON {$on} ");
        return $this;
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws BuilderException
     */
    public function where(array ...$args): static
    {
        $this->position("AND", ...$args);
        return $this;
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws BuilderException
     */
    public function orWhere(array ...$args): static
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
                throw new BuilderException(SystemMessage::INVALID_ARGUMENTS->get());
            [$field, $operator, $value] = $condition;
            if ($index > 0) {
                $clause .= " AND ";
            }
            $clause .= "{$field} {$this->getComparisonOperator($operator)} ?";
            $this->addToBindings($value);
        }
        if (count($args) > 1) {
            $clause .= ")";
        }
        $hasWhere = false;
        foreach ($this->getQuery() as $queryLine) {
            if (str_contains($queryLine, "WHERE"))
                $hasWhere = true;
        }
        $this->addToQuery((!$hasWhere ? " WHERE " : " {$logicalOperator} ") . "{$clause}");
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws BuilderException
     */
    public function orderBy(array ...$args): static
    {
        $clause = '';
        $multipleOrderClauses = count($args) > 1;
        $lastItem = array_key_last($args);
        foreach ($args as $index => $sort) {
            if (count($sort) !== 1 && count($sort) !== 2) {
                throw new BuilderException(SystemMessage::INVALID_ARGUMENTS->get());
            }
            @[$field, $direction] = $sort;
            $clause .= " {$field} {$direction} " . ($multipleOrderClauses && $index !== $lastItem ? ',' : '');
        }
        $hasOrder = false;
        foreach ($this->getQuery() as $queryLine) {
            if (str_contains($queryLine, "ORDER BY")) {
                $hasOrder = true;
                break;
            }
        }
        $this->addToQuery(!$hasOrder ? " ORDER BY {$clause} " : " {$clause} ");
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return $this
     */
    public function limit(?int $limit, ?int $offset = 0): static
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
    public function insert(string $table): static
    {
        $this->setOperation('create');
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery(["INSERT INTO {$table} "]);
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function values(array $values): static
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
    public function update(string $table): static
    {
        $this->setOperation('update');
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery(["UPDATE {$table} "]);
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function set(array $values): static
    {
        $set = [];
        foreach ($values as $field => $value) {
            $this->addToBindings($value);
            $set[] = "{$field} = ?";
        }
        $this->addToQuery(" SET " . implode(', ', $set) . " ");
        return $this;
    }

    /**
     * @return $this
     */
    public function delete(): static
    {
        $this->setOperation('delete');
        $this->clearQuery();
        $this->clearBindings();
        $this->setQuery(["DELETE "]);
        return $this;
    }

    /**
     * @return DatabaseResultCollection
     * @throws BuilderException
     */
    public function build(): DatabaseResultCollection
    {
        try {
            $this->initialize();
            $this->getPDO()->beginTransaction();
            $statement = $this->getPDO()->prepare(implode('', $this->getQuery()));
            $statement->execute($this->getBindings());
            $data = $statement->fetchAll();
            $rowCount = $statement->rowCount();
            $lastInsertId = $this->getOperation() == 'create' ? $this->getPDO()->lastInsertId() : null;
            if ($this->getPDO()->inTransaction())
                $this->getPDO()->commit();
            $this->clearQuery();
            $this->clearBindings();
            $this->setOperation(null);
            return new DatabaseResultCollection($data, $rowCount, $lastInsertId);
        } catch (\Exception|\Throwable $ex) {
            $query = implode('', $this->getQuery());
            $bindings = implode('', $this->getBindings());
            $message = "Exception: {$ex->getMessage()}\r\nQuery: {$query}\r\nBindings: {$bindings}";
            if ($this->getPDO()?->inTransaction())
                $this->getPDO()->rollBack();
            PDOLogger::log($message);
            throw new BuilderException($ex->getMessage());
        }
    }
}