<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2023 Time: 07:36
 */


namespace QMapper\Traits\Model;

trait ModelDelete
{
    /**
     * @return int
     */
    final public function delete(): int
    {
        if ($this->isEmpty() || !$this->getIndexValue())
            return 0;
        static::setBuilderStatement((static::getInstance())::getBuilder()?->setIndex(static::getIndexKey())
            ->delete()->from(static::getTable())
            ->where([static::getIndexKey(), '=', $this->getIndexValue()]));
        return static::remove();
    }

    /**
     * @return static
     */
    final public static function deleteMany(): static
    {
        static::setBuilderStatement((static::getInstance())::getBuilder()?->setIndex(static::getIndexKey())->delete()->from(static::getTable()));
        return static::getInstance();
    }

    /**
     * @return int
     */
    final public static function remove(): int
    {
        $buildStatement = static::handleBuild();
        if (static::isCached() && $buildStatement) {
            static::clearCache();
        }
        return static::getCollection()?->getRowCount() ?? 0;
    }
}