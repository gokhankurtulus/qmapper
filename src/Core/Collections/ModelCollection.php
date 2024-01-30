<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 18:30
 */


namespace QMapper\Core\Collections;

use InvalidArgumentException;
use QMapper\Core\Model;
use QMapper\Interfaces\Jsonable;
use Traversable;

class ModelCollection implements \Iterator, \Countable, Jsonable
{
    private int $position = 0;
    private array $data = [];

    public function __construct(iterable $data)
    {
        if (is_array($data)) {
            $this->data = $data;
        } elseif ($data instanceof Traversable) {
            $this->data = iterator_to_array($data);
        } else {
            throw new InvalidArgumentException("Invalid data type. It should be an array or Traversable.");
        }
        $this->rewind();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): Model
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

    public function append(Model $item): void
    {
        $this->data[] = $item;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function toJson(int $options = 0): string
    {
        $jsonData = [];
        foreach ($this->data as $item) {
            $jsonData[] = $item->toArray();
        }
        return json_encode($jsonData, $options);
    }
}