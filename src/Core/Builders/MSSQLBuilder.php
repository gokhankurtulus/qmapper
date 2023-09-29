<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 22.09.2023 Time: 03:08
 */

namespace QMapper\Core\Builders;

use QMapper\Core\Collection;
use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IBuilder;

class MSSQLBuilder extends PDOBuilder implements IBuilder
{
    /**
     * @throws DatabaseException
     */
    public function initialize(): void
    {
        if (!extension_loaded('pdo_sqlsrv')) throw new DatabaseException(MapperStringTemplate::EXTENSION_REQUIRED->get('pdo_sqlsrv'));
        $this->setPDO($this->createPDO($_ENV['MSSQL_DSN'], $_ENV['MSSQL_USER'], $_ENV['MSSQL_PASSWORD']));
        $this->getPDO()?->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return $this
     */
    public function limit(?int $limit, ?int $offset = null): self
    {
        if (!is_null($offset))
            $this->addToQuery(" OFFSET {$offset} ROWS");
        if (!is_null($limit))
            $this->addToQuery(" FETCH NEXT {$limit} ROWS ONLY");
        return $this;
    }

    public function build(): Collection
    {
        if (in_array($this->getOperation(), $this->getCudOperations())) {
            $this->addToQuery(" SELECT @@ROWCOUNT");
        }
        return parent::build();
    }
}