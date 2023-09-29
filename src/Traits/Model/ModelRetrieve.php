<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2023 Time: 07:33
 */


namespace QMapper\Traits\Model;

use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\ModelException;

trait ModelRetrieve
{
    use Attributes;

    /**
     * @param array $fields
     * @return static
     */
    final public static function select(array $fields = ['*']): static
    {
        static::setBuilderStatement((static::getInstance())::getBuilder()?->setIndex(static::getIndexKey())->select($fields)->from(static::getTable()));
        return static::getInstance();
    }

    /**
     * @param string ...$where
     * @return static
     * @throws \JsonCache\Exceptions\CacheException
     * @throws ModelException
     */
    final public static function find(string ...$where): static
    {
        if (count($where) === 1) {
            $where = [[static::getIndexKey(), '=', $where[0]]];
        } elseif (count($where) === 2) {
            $where = [[$where[0], '=', $where[1]]];
        } elseif (count($where) === 3) {
            $where = [[$where[0], $where[1], $where[2]]];
        }
        static::where(...$where);
        return static::get(1);
    }

    /**
     * @param int|null $limit
     * @return Interactions|array
     * @throws \JsonCache\Exceptions\CacheException|ModelException
     */
    final public static function get(?int $limit = null): static|array
    {
        $queryHash = static::getQueryHash();
        if (static::isCached() && $queryHash && static::handleCache($queryHash) && static::getCollection())
            return static::resolveCollectionData($limit);
        if (!static::getBuilderStatement())
            static::select();
        if ($limit) {
            static::limit($limit);
        }
        static::handleBuild();
        if (static::isCached() && $queryHash && static::getCollection()) {
            static::setCache($queryHash);
        }
        return static::resolveCollectionData($limit);
    }

    /**
     * @param int|null $limit
     * @return static|array
     */
    final protected static function resolveCollectionData(?int $limit = null): static|array
    {
        $entities = [];
        $collectionData = array_slice(static::getCollection()?->getData() ?? [], 0, $limit);
        foreach ($collectionData as $entity) {
            $instance = static::getInstance();
            $instance->assignProperties($entity);
            $entities[] = $instance;
        }

        // TODO this might cause problems because using get() for array might return single object instead of object array
//        return match (count($entities)) {
//            0 => null,
//            1 => $entities[0],
//            default => $entities,
//        };

        if (!empty($entities) && $limit === 1)
            return $entities[0];
        elseif (!empty($entities))
            return $entities;
        else
            return new static();
    }
}