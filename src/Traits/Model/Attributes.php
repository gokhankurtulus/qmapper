<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 10.06.2023 Time: 04:29
 */


namespace QMapper\Traits\Model;

use QMapper\Core\Field;

trait Attributes
{
    protected static string $table = '';
    protected static string $indexKey = 'id';
    protected static bool $cache = false;

    protected mixed $indexValue = '';

    private static array $fields = [];
    private static array $fieldFlags = [];
    private array $properties = [];


    /**
     * Returns table name of entity
     * @return string
     */
    public static function getTable(): string
    {
        return static::$table;
    }

    /**
     * Returns index key of entity
     * @return string
     */
    public static function getIndexKey(): string
    {
        return static::$indexKey;
    }

    /**
     * Returns cache status of entity
     * @return bool
     */
    public static function isCached(): bool
    {
        return static::$cache;
    }

    /**
     * Returns entity's index value
     * @return mixed
     */
    public function getIndexValue(): mixed
    {
        return $this->indexValue;
    }

    /**
     * Set entity's index value
     * @param mixed $indexValue
     * @return void
     */
    private function setIndexValue(mixed $indexValue): void
    {
        $this->indexValue = $indexValue;
    }

    /**
     * @return bool
     */
    public function checkNecessaryModelProperties(): bool
    {
        return static::getTable() && static::getIndexKey();
    }

    /**
     * Set fields from model's schema, if not set.
     * @return void
     */
    private function resolveFields(): void
    {
        if (empty($this->getFields()) && method_exists(static::class, 'schema')) {
            $this->setFields(static::schema());
            /** If fields an indexed array convert to an associative array. */
            $this->setFields(array_combine(
                array_map(fn($field) => $field->getName(), $this->getFields()),
                $this->getFields()
            ));
        }
    }

    private function createProperties()
    {
        if (empty($this->getProperties())) {
            foreach ($this->getFields() as $field) {
                $this->setProperty($field->getName(), $field->getValue());
            }
        }
    }
    /**
     * Assigns properties from given array or object.
     * @param array|object $entityValues
     * @return void
     */
    public function assignProperties(array|object $entityValues = []): void
    {
        foreach ($entityValues as $entityKey => $entityValue) {
            if ($this->isFieldExist($entityKey)) {
                $this->setFieldValue($entityKey, $entityValue);
                $this->setProperty($entityKey, $entityValue);
                if ($entityKey === static::getIndexKey())
                    static::setIndexValue($entityValue);
            }
        }
    }

    /**
     * Returns fields.
     * @return array
     */
    public function getFields(): array
    {
        return static::$fields[static::class] ?? [];
    }

    /**
     * Set fields.
     * @param array $fields
     */
    private function setFields(array $fields): void
    {
        static::$fields[static::class] = $fields;
    }

    /**
     * Returns true if field exist, otherwise returns false.
     * @param string $key
     * @return bool
     */
    public function isFieldExist(string $key): bool
    {
        return array_key_exists($key, $this->getFields());
    }

    /**
     * Returns field if exist, otherwise returns null.
     * @param string $key
     * @return Field|null
     */
    public function getField(string $key): ?Field
    {
        return $this->isFieldExist($key) ? $this->getFields()[$key] : null;
    }

    /**
     * Set field value.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function setFieldValue(string $key, mixed $value): void
    {
        $this->getField($key)?->value($value);
    }

    /**
     * Returns properties.
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Set properties.
     * @param array $properties
     * @return void
     */
    private function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * Returns true if property exist, otherwise returns false.
     * @param string $key
     * @return bool
     */
    public function isPropertyExist(string $key): bool
    {
        return array_key_exists($key, $this->getProperties());
    }

    /**
     * Returns property value.
     * @param string $key
     * @return mixed
     */
    public function getProperty(string $key): mixed
    {
        return $this->isPropertyExist($key) ? $this->getProperties()[$key] : null;
    }

    /**
     * Set property value.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setProperty(string $key, mixed $value): void
    {
        $this->properties[$key] = $value;
    }

    /**
     * Separates field flags (uniques, uuids, requireds, nullables, hiddens, searchables).
     * @return void
     */
    private function separateFieldFlags(): void
    {
        $this->deleteFieldFlags();
        foreach ($this->getFields() as $field) {
            if ($field->isUnique())
                $this->addFieldFlag('uniques', $field->getName());
            if ($field->isRequired())
                $this->addFieldFlag('requireds', $field->getName());
            if ($field->isNullable())
                $this->addFieldFlag('nullables', $field->getName());
            if ($field->isHidden())
                $this->addFieldFlag('hiddens', $field->getName());
            if ($field->isSearchable())
                $this->addFieldFlag('searchables', $field->getName());
            if ($field->isUuid())
                $this->addFieldFlag('uuids', $field->getName());
        }
    }

    /**
     * @return array
     */
    public function getFieldFlags(): array
    {
        return static::$fieldFlags[static::class] ?? [];
    }

    /**
     * @param string $key
     * @param string $flag
     * @return void
     */
    private function addFieldFlag(string $key, string $flag): void
    {
        static::$fieldFlags[static::class][$key][] = $flag;
    }

    /**
     * @param array $propertyFlags
     */
    private function setFieldFlags(array $propertyFlags): void
    {
        static::$fieldFlags[static::class] = $propertyFlags;
    }

    /**
     * @return void
     */
    private function deleteFieldFlags(): void
    {
        static::setFieldFlags([]);
    }

    /**
     * Returns unique fields.
     * @return array
     */
    public function getUniques(): array
    {
        return $this->getFieldFlags()['uniques'] ?? [];
    }

    /**
     * Returns required fields.
     * @return array
     */
    public function getRequireds(): array
    {
        return $this->getFieldFlags()['requireds'] ?? [];
    }

    /**
     * Returns nullable fields.
     * @return array
     */
    public function getNullables(): array
    {
        return $this->getFieldFlags()['nullables'] ?? [];
    }

    /**
     * Returns hidden fields.
     * @return array
     */
    public function getHiddens(): array
    {
        return $this->getFieldFlags()['hiddens'] ?? [];
    }

    /**
     * Returns searchable fields.
     * @return array
     */
    public function getSearchables(): array
    {
        return $this->getFieldFlags()['searchables'] ?? [];
    }

    /**
     * Returns uuid fields.
     * @return array
     */
    public function getUuids(): array
    {
        return $this->getFieldFlags()['uuids'] ?? [];
    }
}