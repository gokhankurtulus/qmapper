<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 05:19
 */


namespace QMapper\Core\Connections;

use PDO;
use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IConnection;

abstract class PDOConnection implements IConnection
{
    protected ?PDO $pdo = null;


    abstract public function initialize(): void;

    public function terminate(): void
    {
        $this->setPDO(null);
    }

    /**
     * @throws DatabaseException
     */
    public function __construct()
    {
        try {
            if (!extension_loaded('pdo')) throw new DatabaseException(MapperStringTemplate::EXTENSION_REQUIRED->get('pdo'));
            $this->initialize();
        } catch (\Exception|\Throwable $exception) {
            throw new DatabaseException($exception->getMessage());
        }
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