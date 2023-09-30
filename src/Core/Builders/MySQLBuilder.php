<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 *  Date: 9.06.2023 Time: 06:38
 */


namespace QMapper\Core\Builders;

use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IBuilder;

class MySQLBuilder extends PDOBuilder implements IBuilder
{
    /**
     * @throws DatabaseException
     */
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_mysql'))
                throw new DatabaseException(MapperStringTemplate::EXTENSION_REQUIRED->get('pdo_mysql'));
            $this->setPDO($this->createPDO($_ENV['MYSQL_DSN'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']));
            $this->getPDO()?->exec("SET NAMES UTF8");
        }
    }
}