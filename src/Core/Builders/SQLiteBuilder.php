<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 24.09.2023 Time: 04:34
 */


namespace QMapper\Core\Builders;

use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IBuilder;

class SQLiteBuilder extends PDOBuilder implements IBuilder
{
    /**
     * @throws DatabaseException
     */
    public function initialize(): void
    {
        if (!extension_loaded('pdo_sqlite')) throw new DatabaseException(MapperStringTemplate::EXTENSION_REQUIRED->get('pdo_sqlite'));
        $this->setPDO($this->createPDO($_ENV['SQLITE_DSN']));
        $this->getPDO()?->exec("PRAGMA encoding = 'UTF-8'");
    }
}