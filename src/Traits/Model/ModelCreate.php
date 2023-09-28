<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2023 Time: 07:35
 */


namespace QMapper\Traits\Model;

trait ModelCreate
{
    /**
     * @param array $fields
     * @return mixed|null
     */
    final public static function create(array $fields = []): mixed
    {
        static::setBuilderStatement((static::getInstance())::getBuilder()?->setIndex(static::getIndexKey())->insert(static::getTable())->values($fields));
        $buildStatement = static::handleBuild();
        if (static::isCached() && $buildStatement) {
            static::clearCache();
        }
        $result = static::getCollection()?->getLastInsertId();
        if ($result)
            static::getInstance()?->assignProperties($fields);
        return $result;
    }
}