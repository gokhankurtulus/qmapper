<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 15.06.2023 Time: 02:08
 */


namespace QMapper\Core\Builders;

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use QMapper\Core\Collection;
use QMapper\Core\Connections\MongoDBConnection;
use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IBuilder;

class MongoDBBuilder extends MongoDBConnection implements IBuilder
{
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
    public function setBindings(array $bindings)
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
     * @return IBuilder
     */
    public function count(): IBuilder
    {
        $this->select(['count' => ['$sum' => 1]]);
        return $this;
    }

    /**
     * @param array $fields
     * @return IBuilder
     */
    public function select(array $fields = ['*']): IBuilder
    {
        $this->clearQuery();
        $this->clearBindings();
        if (!empty($fields) && !in_array('*', $fields)) {
            $this->addToQuery([
                '$project' => array_fill_keys($fields, 1)
            ]);
        }
        return $this;
    }

    /**
     * @param string $table
     * @return IBuilder
     */
    public function from(string $table): IBuilder
    {
        $this->setCollection($this->getDatabase()?->selectCollection($table));
        return $this;
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws DatabaseException
     */
    public function where(array ...$args): self
    {
        $this->position('$and', ...$args);
        return $this;
    }

    /**
     * @param array ...$args
     * @return $this
     * @throws DatabaseException
     */
    public function orWhere(array ...$args): self
    {
        $this->position('$or', ...$args);
        return $this;
    }

    /**
     * @param string $logicalOperator
     * @param array ...$args
     * @return void
     * @throws DatabaseException
     */
    public function position(string $logicalOperator, array ...$args): void
    {
        if (empty($args) || (count($args) === 1 && empty($args[0]))) {
            return;
        }

        $expressions = [];
        foreach ($args as $condition) {
            if (count($condition) !== 3) {
                throw new DatabaseException(MapperStringTemplate::INVALID_ARGUMENTS->get());
            }
            [$field, $operator, $value] = $condition;

            $value = is_numeric($value) ? floatval($value) : $value;

            $expression = [$field => [$this->getCompareOperator($operator) => $value]];
            $expressions[] = $expression;
        }

        if (count($expressions) === 1) {
            $this->addToQuery(['$match' => $expressions[0]]);
        } else {
            $this->addToQuery([
                '$match' => [$logicalOperator => $expressions]
            ]);
        }
    }

    /**
     * @param string $operator
     * @return string
     */
    private function getCompareOperator(string $operator): string
    {
        $operatorMap = [
            '=' => '$eq',
            '!=' => '$ne',
            '>' => '$gt',
            '>=' => '$gte',
            '<' => '$lt',
            '<=' => '$lte',
        ];

        return $operatorMap[$operator] ?? '$eq';
    }

    /**
     * @param array ...$args
     * @return IBuilder
     * @throws DatabaseException
     */
    public function orderBy(array ...$args): IBuilder
    {
        $sort = [];
        foreach ($args as $sortArgs) {
            if (count($sortArgs) !== 1 && count($sortArgs) !== 2) {
                throw new DatabaseException(MapperStringTemplate::INVALID_ARGUMENTS->get());
            }
            @[$field, $direction] = $sortArgs;
            if (empty($direction)) $direction = 'ASC';
            $sort[$field] = $direction === 'ASC' ? 1 : -1;
        }

        $this->addToQuery([
            '$sort' => $sort
        ]);

        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return IBuilder
     */
    public function limit(?int $limit, ?int $offset = 0): IBuilder
    {
        if (!is_null($limit)) {
            $this->addToQuery([
                '$limit' => $limit
            ]);
        }
        if (!is_null($offset)) {
            $this->addToQuery([
                '$skip' => $offset
            ]);
        }
        return $this;
    }

    /**
     * @param string $table
     * @return IBuilder
     */
    public function insert(string $table): IBuilder
    {
        $this->setOperation('create');
        $this->clearQuery();
        $this->clearBindings();
        $this->setCollection($this->getDatabase()?->selectCollection($table));
        return $this;
    }

    /**
     * @param array $values
     * @return IBuilder
     */
    public function values(array $values): IBuilder
    {
        $this->setBindings($values);
        return $this;
    }

    /**
     * @param string $table
     * @return IBuilder
     */
    public function update(string $table): IBuilder
    {
        $this->setOperation('update');
        $this->clearQuery();
        $this->clearBindings();
        $this->setCollection($this->getDatabase()?->selectCollection($table));
        return $this;
    }

    /**
     * @param array $values
     * @return IBuilder
     */
    public function set(array $values): IBuilder
    {
        $this->setBindings([
            '$set' => $values
        ]);
        return $this;
    }

    /**
     * @return IBuilder
     */
    public function delete(): IBuilder
    {
        $this->setOperation('delete');
        $this->clearQuery();
        $this->clearBindings();
        return $this;
    }

    /**
     * @return Collection
     * @throws DatabaseException
     */
    public function build(): Collection
    {
        try {
            $filter = $this->getQuery();
            $this->changeIndexKeyToObjectKey($filter);
            $this->setQuery($filter);
            if (in_array($this->getOperation(), $this->getCudOperations())) {
                [$data, $rowCount, $lastInsertId] = $this->handleCUD();
            } else {
                [$data, $rowCount, $lastInsertId] = $this->handleRead();
            }
            return new Collection($data, $rowCount, $lastInsertId);
        } catch (\Exception|\Throwable $ex) {
            throw new DatabaseException($ex->getMessage());
        }
    }

    /**
     * @return array
     * @throws DatabaseException
     */
    private function handleCUD(): array
    {
        $data = [];
        $row_count = 0;
        $last_insert_id = null;

        $bulk = new BulkWrite();
        $filter = $this->getQuery();
        $this->removeMatchInFilter($filter);

        if ($this->getOperation() === 'create') {
            if (!$this->getIndex())
                throw new DatabaseException("Index key is not set.");
            $last_insert_id = (string)$bulk->insert(array_merge($this->getBindings(), [$this->getIndex() => new ObjectId()]));
        } elseif ($this->operation === 'update') {
            $bulk->update($filter, $this->getBindings(), ['multi' => true]);
        } elseif ($this->operation === 'delete') {
            $bulk->delete($filter);
        }

        $databaseName = $this->getDatabase()->getDatabaseName();
        $collectionName = $this->getCollection()->getCollectionName();
        $result = $this->getManager()->executeBulkWrite("$databaseName.$collectionName", $bulk);

        if ($this->getOperation() === 'create') {
            $row_count = $result->getInsertedCount();
        } elseif ($this->getOperation() === 'update') {
            $row_count = $result->getModifiedCount();
        } elseif ($this->getOperation() === 'delete') {
            $row_count = $result->getDeletedCount();
        }
        return [
            $data,
            $row_count,
            $last_insert_id,
        ];
    }

    /**
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    private function handleRead(): array
    {
        $data = [];
        $last_insert_id = null;
        $pipeline = $this->getQuery();
        $command = new Command(['aggregate' => $this->getCollection()->getCollectionName(), 'pipeline' => $pipeline, 'cursor' => new \stdClass()]);
        $cursor = $this->getManager()->executeCommand($this->getDatabase()->getDatabaseName(), $command);

        foreach ($cursor as $cursorItem) {
            $dataItem = new \stdClass();
            $variables = get_object_vars($cursorItem);
            foreach ($variables as $variableKey => $variable) {
                if ($variableKey === $this->getIndex())
                    $dataItem->{$variableKey} = (string)$variable;
                else
                    $dataItem->{$variableKey} = $variable;
            }
            $data[] = $dataItem;
            $last_insert_id = (string)$cursorItem->_id;
        }
        $row_count = count($data);
        return [
            $data,
            $row_count,
            $last_insert_id,
        ];
    }

    /**
     * @param array $data
     * @return void
     */
    private function changeIndexKeyToObjectKey(array &$data): void
    {
        foreach ($data as &$item) {
            if (is_iterable($item)) {
                if (isset($item[$this->getIndex()]) && is_array($item[$this->getIndex()])) {
                    foreach ($item[$this->getIndex()] as &$indexItem)
                        if (ctype_xdigit($indexItem) && strlen($indexItem) === 24)
                            $indexItem = new ObjectId($indexItem);
                }
                $this->changeIndexKeyToObjectKey($item);
            }
        }
    }

    /**
     * @param array $data
     * @return void
     */
    private function removeMatchInFilter(array &$data): void
    {
        $newData = [];
        foreach ($data as &$item) {
            if (is_array($item)) {
                if (isset($item['$match']) && is_array([$item['$match']])) {
                    $newData = array_merge($newData, $item['$match']);
                }
            }
        }
        $data = $newData;
    }
}