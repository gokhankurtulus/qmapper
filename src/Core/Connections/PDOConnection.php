<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:22
 */

namespace QMapper\Core\Connections;

use PDO;
use QMapper\Core\Connection;
use QMapper\Interfaces\IConnection;
use QMapper\Enums\SystemMessage;
use QMapper\Exceptions\ConnectionException;

abstract class PDOConnection extends Connection implements IConnection
{
    abstract public function initialize(): void;

    protected ?PDO $pdo = null;

    public function terminate(): void
    {
        $this->setPDO(null);
    }

    /**
     * @throws ConnectionException
     */
    public function __construct()
    {
        if (!extension_loaded('pdo'))
            throw new ConnectionException(SystemMessage::EXTENSION_REQUIRED->get('pdo'));
    }

    public function __destruct()
    {
        $this->terminate();
    }

    /**
     * @return PDO|null
     */
    protected function getPDO(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @param PDO|null $pdo
     * @return void
     */
    protected function setPDO(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    protected function createPDO(string $dsn, string $user = "", string $password = ""): PDO
    {
        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
}