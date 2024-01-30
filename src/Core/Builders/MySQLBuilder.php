<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:23
 */


namespace QMapper\Core\Builders;

use QMapper\Core\Builders\Base\PDOBuilder;
use QMapper\Interfaces\IBuilder;
use QMapper\Enums\SystemMessage;
use QMapper\Exceptions\BuilderException;

class MySQLBuilder extends PDOBuilder implements IBuilder
{
    /**
     * @throws BuilderException
     */
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_mysql'))
                throw new BuilderException(SystemMessage::EXTENSION_REQUIRED->get('pdo_mysql'));
            $this->setPDO($this->createPDO($_ENV['MYSQL_DSN'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']));
            $this->getPDO()?->exec("SET NAMES UTF8");
        }
    }
}