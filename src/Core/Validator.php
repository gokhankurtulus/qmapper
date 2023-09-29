<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 23.06.2023 Time: 02:45
 */


namespace QMapper\Core;

use QMapper\Enums\MapperStringTemplate;
use QMapper\Exceptions\AttributeException;

class Validator
{
    private Model $model;
    private array $fields;

    /**
     * @param Model $model
     * @param array $fields
     */
    public function __construct(Model $model, array $fields)
    {
        $this->setModel($model);
        $this->setFields($fields);
    }


    /**
     * @param bool $returnDiff
     * @return array|bool
     */
    public function hasDiff(bool $returnDiff = false): array|bool
    {
        $diff = [];
        // If there is no property we don't need to check by key name.
        if (!$this->getModel()?->getProperties()) {
            if (!$returnDiff)
                return true;
            return array_keys($this->getFields());
        }

        foreach ($this->getFields() as $field => $value) {
            $isPropertyExist = array_key_exists($field, $this->getModel()?->getProperties());
            $isFieldValueMatches = $this->getModel()?->getField($field)?->getValue() === $value;
            if (!$isPropertyExist || !$isFieldValueMatches) {
                if (!$returnDiff) {
                    return true;
                }
                if (!in_array($field, $diff)) {
                    $diff[] = $field;
                }
            }
        }
        if ($returnDiff && !empty($diff))
            return $diff;

        return false;
    }

    /**
     * @return bool
     * @throws AttributeException
     */
    public function validate(): bool
    {
        $mergedData = array_merge($this->getModel()?->getProperties(), $this->getFields());
        if (empty($mergedData))
            throw new AttributeException(MapperStringTemplate::DATA_ISNOT_SENT_CORRECT->get());

        foreach ($this->getModel()?->getRequireds() as $requiredKey) {
            if (!array_key_exists($requiredKey, $mergedData) && $this->getModel()?->getField($requiredKey))
                throw new AttributeException(MapperStringTemplate::FIELD_IS_REQUIRED->get($requiredKey));
        }
        $this->getModel()?->assignProperties($mergedData);

        foreach ($mergedData as $key => $value) {
            if (array_key_exists($key, $this->getModel()?->getProperties()))
                $this->validateKey($key, $value);
            else
                throw new AttributeException(MapperStringTemplate::FIELD_DOESNT_EXIST->get($key, basename($this->getModel()::class)));
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @return bool|null
     * @throws AttributeException
     */
    public function validateKey($key, $value): ?bool
    {
        return $this->getModel()?->getField($key)?->validate($value, $this->getModel()?->getFields());
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     */
    protected function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}