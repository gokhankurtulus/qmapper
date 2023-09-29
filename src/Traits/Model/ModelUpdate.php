<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2023 Time: 07:36
 */


namespace QMapper\Traits\Model;

trait ModelUpdate
{
    /**
     * @param array $fields
     * @return int
     */
    final public function update(array $fields = []): int
    {
        if ($this->isEmpty() || empty($fields) || !$this->getIndexValue())
            return 0;
        static::setBuilderStatement((static::getInstance())::getBuilder()?->setIndex(static::getIndexKey())
            ->update(static::getTable())->set($fields)
            ->where([static::getIndexKey(), '=', $this->getIndexValue()]));
        $result = static::set();
        if ($result)
            $this->assignProperties($fields);
        return $result;
    }

    /**
     * @param array $fields
     * @return static
     */
    final public static function updateMany(array $fields = []): static
    {
        static::setBuilderStatement((static::getInstance())::getBuilder()?->setIndex(static::getIndexKey())->update(static::getTable())->set($fields));
        return static::getInstance();
    }

    /**
     * @return int
     */
    final public static function set(): int
    {
        $buildStatement = static::handleBuild();
        if (static::isCached() && $buildStatement) {
            static::clearCache();
        }
        return static::getCollection()?->getRowCount() ?? 0;
    }

}