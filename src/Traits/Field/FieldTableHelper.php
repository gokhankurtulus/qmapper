<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 21.07.2023 Time: 00:46
 */


namespace QMapper\Traits\Field;

trait FieldTableHelper
{
    private bool $store = true;
    private bool $increament = false;
    private bool $primary = false;
    private bool $unique = false;

    private bool $required = false;
    private bool $nullable = false;
    private bool $hidden = false;
    private bool $identifier = false;
    private bool $searchable = false;

    private ?string $default = null;
    private ?string $extra = null;
    private ?string $comment = null;
    private ?string $regex = null;
    private ?string $match = null;

    /**
     * Get the field is storable.
     * @return bool
     */
    public function isStorable(): bool
    {
        return $this->store;
    }

    /**
     * Set the field as no stored,
     * so it won't be stored but field's data can be able to process.
     * @return $this
     */
    public function nostore(): static
    {
        $this->store = false;
        return $this;
    }

    /**
     * Get the field has auto-increment.
     * @return bool
     */
    public function hasIncreament(): bool
    {
        return $this->increament;
    }

    /**
     * Set the field as auto-incremented.
     * @return $this
     */
    public function increament(): static
    {
        $this->increament = true;
        return $this;
    }

    /**
     * Get the field is primary-key.
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * Set the field as primary-key.
     * @return $this
     */
    public function primary(): static
    {
        $this->primary = true;
        return $this;
    }

    /**
     * Get the field is unique.
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * Set the field as unique.
     * @return $this
     */
    public function unique(): static
    {
        $this->unique = true;
        return $this;
    }

    /**
     * Get the field is required.
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Set the field as required,
     * so an entity always have to carry this field's value.
     * @return $this
     */
    public function required(): static
    {
        $this->required = true;
        return $this;
    }

    /**
     * Get the field is nullable.
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Set the field as nullable,
     * so field's value can be null or empty.
     * @return $this
     */
    public function nullable(): static
    {
        $this->nullable = true;
        $this->default("NULL");
        return $this;
    }

    /**
     * Get the field is hidden.
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set the field as hidden,
     * field is stored and can be able to process
     * it allows you to hide this data
     * when you want to use a specific output instead of full entity.
     * @return $this
     */
    public function hidden(): static
    {
        $this->hidden = true;
        return $this;
    }

    /**
     * Get the field is identifier.
     * @return bool
     */
    public function isIdentifier(): bool
    {
        return $this->identifier;
    }

    /**
     * Set the field as identifier,
     * so entity can be found using this key.
     * e.g: 'product slug'. You might want to find products by slug,
     * so 'slug' can be identifier,
     * @return $this
     */
    public function identifier(): static
    {
        $this->identifier = true;
        return $this;
    }

    /**
     * Get the field is searchable.
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Set the field as searchable,
     * works similar to identifier() method,
     * but while identifier() requires an exact match
     * searchable() focuses on searching for similarities.
     * @return $this
     */
    public function searchable(): static
    {
        $this->searchable = true;
        return $this;
    }

    /**
     * Get field's default.
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * Set field's default.
     * @param string $default
     * @return $this
     */
    public function default(string $default): static
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Get field's extra.
     * @return string|null
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * Set field's extra.
     * @param string $extra
     * @return $this
     */
    public function extra(string $extra): static
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * Get field's comment.
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set field's comment.
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get field's regex.
     * @return string|null
     */
    public function getRegex(): ?string
    {
        return $this->regex;
    }

    /**
     * Set field's regex for validation.
     * @param string|null $regex
     * @return $this
     */
    public function regex(?string $regex): static
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * Get the key name of the field which should have the same value.
     * @return string|null
     */
    public function getMatch(): ?string
    {
        return $this->match;
    }

    /**
     * The field requires having the same value as the field given the key.
     * @param string|null $match
     * @return $this
     */
    public function match(?string $match): static
    {
        $this->match = $match;
        return $this;
    }
}