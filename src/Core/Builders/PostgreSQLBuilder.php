<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 24.09.2023 Time: 04:34
 */


namespace QMapper\Core\Builders;

use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IBuilder;

class PostgreSQLBuilder extends PDOBuilder implements IBuilder
{
    /**
     * @throws DatabaseException
     */
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_pgsql'))
                throw new DatabaseException(MapperStringTemplate::EXTENSION_REQUIRED->get('pdo_pgsql'));
            $this->setPDO($this->createPDO($_ENV['PGSQL_DSN'], $_ENV['PGSQL_USER'], $_ENV['PGSQL_PASSWORD']));
            $this->getPDO()?->exec("SET NAMES UTF8");
        }
    }
}