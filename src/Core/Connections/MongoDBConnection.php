<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 15.06.2023 Time: 02:08
 */


namespace QMapper\Core\Connections;

use MongoDB\Driver\Manager;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\DatabaseException;
use QMapper\Interfaces\IConnection;

abstract class MongoDBConnection implements IConnection
{
    protected ?Manager $manager = null;
    protected ?Client $client = null;
    protected ?Database $database = null;
    protected ?Collection $collection = null;

    /**
     * @throws DatabaseException
     */
    public function __construct()
    {
        try {
            if (!extension_loaded('mongodb')) throw new DatabaseException(MapperStringTemplate::EXTENSION_REQUIRED->get('mongodb'));
            $this->initialize();
        } catch (\Exception|\Throwable $exception) {
            throw new DatabaseException($exception->getMessage());
        }
    }

    public function __destruct()
    {
        $this->terminate();
    }

    public function initialize(): void
    {
        $this->setManager(new Manager($_ENV['MONGODB_DSN']));
        $this->setClient(new Client($_ENV['MONGODB_DSN']));
        $this->setDatabase($this->getClient()?->selectDatabase($_ENV['MONGODB_DATABASE']));
    }

    public function terminate(): void
    {
        $this->setManager(null);
        $this->setClient(null);
        $this->setDatabase(null);
        $this->setCollection(null);
    }

    /**
     * @return Manager|null
     */
    protected function getManager(): ?Manager
    {
        return $this->manager;
    }

    /**
     * @param Manager|null $manager
     */
    protected function setManager(?Manager $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * @return Client|null
     */
    protected function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client|null $client
     */
    protected function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return Database|null
     */
    protected function getDatabase(): ?Database
    {
        return $this->database;
    }

    /**
     * @param Database|null $database
     */
    protected function setDatabase(?Database $database): void
    {
        $this->database = $database;
    }

    /**
     * @return Collection|null
     */
    protected function getCollection(): ?Collection
    {
        return $this->collection;
    }

    /**
     * @param Collection|null $collection
     */
    protected function setCollection(?Collection $collection): void
    {
        $this->collection = $collection;
    }
}