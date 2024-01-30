<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:47
 */


namespace QMapper\Core\Collections;

use InvalidArgumentException;
use QMapper\Interfaces\Jsonable;
use Traversable;

class DatabaseResultCollection implements \Iterator, \Countable, Jsonable
{
    private int $position = 0;
    private array $data;
    private int $rowCount;
    private mixed $lastInsertId;

    public function __construct(iterable $data, int $rowCount, mixed $lastInsertId)
    {
        if (is_array($data)) {
            $this->data = $data;
        } elseif ($data instanceof Traversable) {
            $this->data = iterator_to_array($data);
        } else {
            throw new InvalidArgumentException("Invalid data type. It should be an array or Traversable.");
        }
        $this->rowCount = $rowCount;
        $this->lastInsertId = $lastInsertId;
        $this->rewind();
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getLastInsertId(): mixed
    {
        return $this->lastInsertId;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): mixed
    {
        return $this->data[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function append(mixed $item): void
    {
        $this->data[] = $item;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->data, $options);
    }
}