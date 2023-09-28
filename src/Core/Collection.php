<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 12.07.2023 Time: 22:58
 */


namespace QMapper\Core;

final class Collection
{
    private array $data = [];
    private int $rowCount = 0;
    private mixed $lastInsertId = null;


    public function __construct(?array $data, ?int $rowCount, int|string|null $lastInsertId)
    {
        $this->setData($data ?? []);
        $this->setRowCount($rowCount ?? 0);
        $this->setLastInsertId($lastInsertId);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * @param int $rowCount
     */
    public function setRowCount(int $rowCount): void
    {
        $this->rowCount = $rowCount;
    }

    /**
     * @return mixed|null
     */
    public function getLastInsertId(): mixed
    {
        return $this->lastInsertId;
    }

    /**
     * @param mixed|null $lastInsertId
     */
    public function setLastInsertId(mixed $lastInsertId): void
    {
        $this->lastInsertId = $lastInsertId;
    }
}