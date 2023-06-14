<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 05:19
 */


namespace QMapper\Core\Connections;

use PDO;
use PDOException;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\DBMSInterface;

class PDOConnection implements DBMSInterface
{
    protected PDO $pdo;
    protected string $engine = "";
    protected string $charset = "";
    protected string $collate = "";
    protected string $query = '';
    protected array $bindings = [];

    /**
     * @throws DatabaseException
     */
    public function __construct()
    {
        $this->setEngine($_ENV['DB_ENGINE']);
        $this->setCharset($_ENV['DB_CHARSET']);
        $this->setCollate($_ENV['DB_COLLATE']);
        try {
            $this->setPDO();
        } catch (PDOException $exception) {
            throw new DatabaseException($exception->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * @return bool
     */
    public function setPDO(): bool
    {
        $this->pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return true;
    }

    /**
     * @return string
     */
    public function getEngine(): string
    {
        return $this->engine;
    }

    /**
     * @param string $engine
     */
    public function setEngine(string $engine): void
    {
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    /**
     * @return string
     */
    public function getCollate(): string
    {
        return $this->collate;
    }

    /**
     * @param string $collate
     */
    public function setCollate(string $collate): void
    {
        $this->collate = $collate;
    }
}