<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 10.06.2023 Time: 04:29
 */


namespace QMapper\Traits\Model;

use QMapper\Core\Field;
use QMapper\Core\Model;

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
            /** Get keys of fields*/
                array_map(fn($field) => $field->getName(), $this->getFields()),
                /** Use values of fields*/
                $this->getFields()
            ));
        }
    }

    /**
     * Sets field's values to null.
     * @return void
     */
    private function resetFieldValues(): void
    {
        array_map(fn($field) => $field->value(null), $this->getFields());
    }

    /**
     * Returns fields.
     * @return Field[] array
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
     * Returns field if exists, otherwise returns null.
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
     * Create properties from fields array.
     * @return void
     */
    private function createProperties(): void
    {
        if (empty($this->getProperties())) {
            foreach ($this->getFields() as $field) {
                $this->setProperty($field->getName(), $field->getValue());
            }
        }
    }

    /**
     * Sets properties values to null.
     * @return void
     */
    private function resetPropertyValues(): void
    {
        array_map(fn($propertyKey) => $this->setProperty($propertyKey, null), array_keys($this->getProperties()));
    }

    /**
     * Assigns properties and field value from given array or object.
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
     * Returns true if property exist, otherwise returns false.
     * @param string $key
     * @return bool
     */
    public function isPropertyExist(string $key): bool
    {
        return array_key_exists($key, $this->getProperties());
    }

    /**
     * Change properties to allow tree view from fields related with models and can be reachable in properties.
     * @param bool $hideHiddenFields If true sets property value as array from related model, but object can't be reachable.
     * @return $this
     */
    public function tree(bool $hideHiddenFields = false): static
    {
        $relations = $this->getRelations();
        /** If there is no relation return $this.*/
        if (!$relations)
            return $this;
        foreach ($relations as $relation) {
            $fieldHasRelation = $this->getField($relation);
            /** If marked as related but field doesn't exist continue to next relation. */
            if (!$fieldHasRelation)
                continue;
            /** Get model and column key from related array.*/
            [$model, $key] = $fieldHasRelation->getRelated();
            if (class_exists($model) && is_string($key)) {
                /** Find relatedModel with column key and value from field which has relation.*/
                /** @var Model $relatedModel */
                $relatedModel = $model::find($key, $fieldHasRelation->getValue());
                /** Set relation property value to relatedModel if model is not empty.*/
                if (!$relatedModel->isEmpty()) {
                    if ($hideHiddenFields)
                        $this->setProperty($relation, $relatedModel->toArray());
                    else
                        $this->setProperty($relation, $relatedModel);
                }
            }
        }
        return $this;
    }

    /**
     * Separates field flags (uniques, uuids, requireds, nullables, hiddens, searchables, relations).
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
            if ($field->hasRelation())
                $this->addFieldFlag('relations', $field->getName());
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
     * @param array $propertyFlags
     */
    private function setFieldFlags(array $propertyFlags): void
    {
        static::$fieldFlags[static::class] = $propertyFlags;
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

    /**
     * Returns relation fields.
     * @return array
     */
    public function getRelations(): array
    {
        return $this->getFieldFlags()['relations'] ?? [];
    }
}