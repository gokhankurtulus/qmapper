<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 18:30
 */

namespace QMapper\Traits\Model;

use QMapper\Core\Collections\DatabaseResultCollection;
use QMapper\Core\Collections\ModelCollection;
use QMapper\Enums\SystemMessage;
use QMapper\Exceptions\ModelException;
use QMapper\Loggers\ModelLogger;

trait ModelInteractions
{
    use ModelAttributes;

    /** @var bool $stateStarted state for checking the chaining methods have started properly */
    protected static bool $stateStarted = false;
    /** @var array $order order state to set order by index key for use limit() method without using order() method */
    protected static array $order = [];


    /**
     * Find an entity
     * @param string $value value to search.
     * @param string|null $key key to search. Default: Model's index key.
     * @param string|null $operator comparison operator. Can be ["=", "!=", "<>", ">", ">=", "<", ">="]. Default: "="
     * @return ModelCollection
     * @throws ModelException
     */
    final public static function find(string $value, ?string $key = null, ?string $operator = null): ModelCollection
    {
        if (is_null($key)) {
            if (!static::getIndexKey()) {
                ModelLogger::log(SystemMessage::DOES_NOT_EXIST->get('index key in ' . basename(static::class)));
                throw new ModelException(SystemMessage::DOES_NOT_EXIST->get('index key'));
            }
            $key = static::getIndexKey();
        }
        if (is_null($operator))
            $operator = "=";
        static::where([$key, $operator, $value]);
        return static::get(1);
    }

    /**
     * Get records as ModelCollection. Then all model methods will be available for each entity.
     * @param int|null $limit limit for get how many records
     * @param int|null $offset offset for limit
     * @return ModelCollection
     * @throws ModelException
     * @throws \Exception
     */
    final public static function get(?int $limit = null, ?int $offset = 0): ModelCollection
    {
        $entities = new ModelCollection([]);
        if (!is_null($limit))
            static::limit($limit, $offset);
        $returnedData = static::build();
        foreach ($returnedData as $returnedEntity) {
            $instance = static::getInstance();
            $instance->assignProperties($returnedEntity);
            $entities->append($instance);
        }
        return $entities;
    }

    final public static function select(array $fields = ['*']): static
    {
        static::getBuilder()?->setIndex(static::getIndexKey())
            ->select($fields)
            ->from(static::getTable());
        static::setStateStarted(true);
        return static::getInstance();
    }

    final public static function count(array $fields = ['*']): static
    {
        static::getBuilder()
            ?->count($fields)
            ->from(static::getTable());
        static::setStateStarted(true);
        return static::getInstance();
    }

    final public static function as(string $as): static
    {
        static::getBuilder()?->as($as);
        return static::getInstance();
    }

    final public static function innerJoin(string $table, string $on): static
    {
        static::getBuilder()?->innerJoin($table, $on);
        return static::getInstance();
    }

    final public static function leftJoin(string $table, string $on): static
    {
        static::getBuilder()?->leftJoin($table, $on);
        return static::getInstance();
    }

    final public static function rightJoin(string $table, string $on): static
    {
        static::getBuilder()?->rightJoin($table, $on);
        return static::getInstance();
    }

    final public static function fullJoin(string $table, string $on): static
    {
        static::getBuilder()?->fullJoin($table, $on);
        return static::getInstance();
    }

    final public static function where(array ...$where): static
    {
        if (!static::isStateStarted())
            static::select();
        static::getBuilder()?->where(...$where);
        return static::getInstance();
    }

    final public static function orWhere(array ...$orWhere): static
    {
        if (!static::isStateStarted())
            static::select();
        static::getBuilder()?->orWhere(...$orWhere);
        return static::getInstance();
    }


    /**
     * @param array ...$sort for the sorts the records by given key or keys. Second parameter (ASC, DESC) of each array is optional Example: order(["id", "DESC"], ["name", "ASC"])
     * @return static
     */
    final public static function order(array ...$sort): static
    {
        static::getBuilder()?->orderBy(...$sort);
        static::setOrder(...$sort);
        return static::getInstance();
    }


    /**
     * @param int|null $limit limit for get how many records
     * @param int|null $offset offset for limit
     * @return static
     * @throws ModelException
     */
    final public static function limit(?int $limit, ?int $offset = 0): static
    {
        if (empty(static::getOrder())) {
            if (!static::getIndexKey()) {
                ModelLogger::log(SystemMessage::DOES_NOT_EXIST->get('index key in ' . basename(static::class)));
                throw new ModelException(SystemMessage::DOES_NOT_EXIST->get('index key'));
            }
            static::order([static::getIndexKey()]);
        }
        static::getBuilder()?->limit($limit, $offset);
        return static::getInstance();
    }


    /**
     * @param array $fields fields for the creating an entity
     * @return mixed Last inserted id of table
     */
    final public static function create(array $fields = []): mixed
    {
        static::setStateStarted(true);
        $result = static::getBuilder()?->setIndex(static::getIndexKey())
            ->insert(static::getTable())
            ->values($fields)
            ->build()->getLastInsertId();
        if ($result)
            static::getInstance()->assignProperties($fields);
        return $result;
    }

    /**
     * Can be used for update multiple rows in same model. The condition must be chained with the where() or orWhere() methods then execute the set() method.
     * Full example: Model::updateMany($fields)::where($where)::set();
     * @param array $fields fields to update
     * @return $this
     */
    final static public function updateMany(array $fields): static
    {
        static::getBuilder()?->setIndex(static::getIndexKey())
            ->update(static::getTable())
            ->set($fields);
        static::setStateStarted(true);
        return static::getInstance();
    }


    /**
     * Can be used for update single object. Object must have index value for update.
     * @param array $fields fields to update
     * @return int affected rows count
     * @throws ModelException
     */
    final public function update(array $fields = []): int
    {
        if ($this->isEmpty() || empty($fields) || !$this->getIndexValue())
            return 0;
        static::getBuilder()?->setIndex(static::getIndexKey())
            ->update(static::getTable())
            ->set($fields)
            ->where([static::getIndexKey(), '=', $this->getIndexValue()]);
        static::setStateStarted(true);
        $result = static::set();
        if ($result)
            $this->assignProperties($fields);
        return $result;
    }

    /**
     * Ending method for update() and updateMany() methods
     * @return int affected rows count
     * @throws ModelException
     */
    final public static function set(): int
    {
        return static::build()->getRowCount();
    }

    /**
     *  Can be used for delete multiple rows in same model. The condition must be chained with the where() or orWhere() methods then execute the remove() method.
     *  Full example: Model::deleteMany()::where($where)::remove();
     * @return static
     */
    final static public function deleteMany(): static
    {
        static::getBuilder()?->setIndex(static::getIndexKey())
            ->delete()
            ->from(static::getTable());
        static::setStateStarted(true);
        return static::getInstance();
    }

    /**
     * Can be used for delete single object. Object must have index value for delete.
     * @return int affected rows count
     * @throws ModelException
     */
    final public function delete(): int
    {
        if ($this->isEmpty() || !$this->getIndexValue())
            return 0;
        static::getBuilder()?->setIndex(static::getIndexKey())
            ->delete()
            ->from(static::getTable())
            ->where([static::getIndexKey(), '=', $this->getIndexValue()]);
        static::setStateStarted(true);
        return static::remove();
    }

    /**
     * Ending method for delete() and deleteMany() methods
     * @return int affected rows count
     * @throws ModelException
     */
    final public static function remove(): int
    {
        return static::build()->getRowCount();
    }

    /**
     * Execute the chained methods.
     * @return DatabaseResultCollection
     * @throws ModelException if there is no starter methods used yet
     */
    final public static function build(): DatabaseResultCollection
    {
        if (!static::isStateStarted()) {
            ModelLogger::log(SystemMessage::FAILED_TO->get('build. There is no started state for builder in ' . basename(static::class)));
            throw new ModelException(SystemMessage::FAILED_TO->get('build. There is no started state for builder'));
        }
        static::setStateStarted(false);
        static::setOrder([]);
        return static::getBuilder()->build();
    }

    /**
     * @return bool
     */
    final protected static function isStateStarted(): bool
    {
        return static::$stateStarted;
    }

    /**
     * @param bool $stateStarted
     * @return void
     */
    final protected static function setStateStarted(bool $stateStarted): void
    {
        static::$stateStarted = $stateStarted;
    }

    /**
     * @return array
     */
    final protected static function getOrder(): array
    {
        return static::$order;
    }

    /**
     * @param array $order
     * @return void
     */
    final protected static function setOrder(array $order): void
    {
        static::$order = $order;
    }
}