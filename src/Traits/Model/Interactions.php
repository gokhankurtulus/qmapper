<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 10.06.2023 Time: 04:26
 */


namespace QMapper\Traits\Model;

use JsonCache\JsonCache;
use QMapper\Core\Collection;
use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\ModelException;

trait Interactions
{
    use Attributes;
    use ModelCreate;
    use ModelRetrieve;
    use ModelUpdate;
    use ModelDelete;

    protected static array $order = [];
    protected static ?Collection $collection = null;
    protected static mixed $builderStatement = null;

    /**
     * @return static|null
     */
    final public static function getInstance(): ?static
    {
        $instance = new static();
        $instance->resolveFields();
        $instance->separateFieldFlags();
        $instance->createProperties();
        return $instance;
    }

    /**
     * @param array ...$where
     * @return static
     */
    final public static function where(array ...$where): static
    {
        if (!static::getBuilderStatement())
            static::select();
        static::setBuilderStatement(static::getBuilderStatement()?->where(...$where));
        return static::getInstance();
    }

    /**
     * @param array ...$orWhere
     * @return static
     */
    final public static function orWhere(array ...$orWhere): static
    {
        if (!static::getBuilderStatement())
            static::select();
        static::setBuilderStatement(static::getBuilderStatement()?->orWhere(...$orWhere));
        return static::getInstance();
    }

    /**
     * @param array ...$sort
     * @return static
     */
    final public static function order(array ...$sort): static
    {
        if (static::getBuilderStatement()) {
            static::setBuilderStatement(static::getBuilderStatement()?->orderBy(...$sort));
            static::setOrder(...$sort);
        }
        return static::getInstance();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return static
     * @throws ModelException
     */
    final public static function limit(?int $limit, ?int $offset = 0): static
    {
        if (empty(static::getOrder())) {
            if (!static::getIndexKey())
                throw new ModelException(MapperStringTemplate::MODEL_DOESNT_HAVE->get(basename(static::class), 'index key'));
            static::order([static::getIndexKey()]);
        }
        if (static::getBuilderStatement())
            static::setBuilderStatement(static::getBuilderStatement()?->limit($limit, $offset));
        return static::getInstance();
    }

    /**
     * @return bool
     */
    final protected static function handleBuild(): bool
    {
        static::setCollection(static::getBuilderStatement()?->build());
        static::setNullStatement();
        static::setOrder([]);
        return (bool)static::getCollection();
    }

    /**
     * @param string $key
     * @return bool
     */
    final protected static function handleCache(string $key): bool
    {
        $cacheData = static::getDataFromCache($key);
        if (!$cacheData)
            return false;
        static::setCollection(new Collection($cacheData["data"], $cacheData['rowCount'], $cacheData['lastInsertId']));
        static::setNullStatement();
        static::setOrder([]);
        return (bool)static::getCollection();
    }

    /**
     * @param string $queryHash
     * @return mixed
     */
    final protected static function getDataFromCache(string $queryHash): mixed
    {
        try {
            $cachedData = null;
            $cache = new JsonCache();
            if ($cache->has($queryHash))
                $cachedData = $cache->get($queryHash);
            return $cachedData;
        } catch (\Exception|\Throwable $exception) {
            return null;
        }
    }

    /**
     * @param string $key
     * @return bool
     * @throws \JsonCache\Exceptions\CacheException
     */
    final protected static function setCache(string $key): bool
    {
        $cacheData = [
            'data' => static::getCollection()?->getData(),
            'rowCount' => static::getCollection()?->getRowCount(),
            'lastInsertId' => static::getCollection()?->getLastInsertId(),
        ];
        $cache = new JsonCache();
        if (!$cache->has($key))
            return $cache->set($key, $cacheData);
        return false;
    }

    /**
     * @return bool
     */
    final protected static function clearCache(): bool
    {
        $cache = new JsonCache();
        $cache->clear();
        return true;
    }

    /**
     * @return false|string
     */
    final protected static function getQueryHash(): string|false
    {
        $query = static::getBuilderStatement()?->getQuery();
        if (is_array($query))
            $query = print_r($query, true);
        $bindings = static::getBuilderStatement()?->getBindings();
        return hash('sha256', $query . print_r($bindings, true));
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

    /**
     * @return Collection|null
     */
    final protected static function getCollection(): ?Collection
    {
        return static::$collection;
    }

    /**
     * @param Collection|null $collection
     */
    final protected static function setCollection(?Collection $collection): void
    {
        static::$collection = $collection;
    }


    /**
     * @return mixed|null
     */
    final protected static function getBuilderStatement(): mixed
    {
        return static::$builderStatement;
    }

    /**
     * @param mixed|null $builderStatement
     */
    final protected static function setBuilderStatement(mixed $builderStatement): void
    {
        static::$builderStatement = $builderStatement;
    }

    /**
     * @return void
     */
    final protected static function setNullStatement(): void
    {
        static::setBuilderStatement(null);
    }
}