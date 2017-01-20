<?php

namespace Iatstuti\Database\Support;

/**
 * Nullable (database) fields trait.
 *
 * Include this trait in any Eloquent models you wish to automatically set
 * empty field values to null on. When saving, iterate over the model's
 * attributes and if their value is empty, make it null before save.
 *
 * @copyright  2015 IATSTUTI
 * @author     Michael Dyrynda <michael@dyrynda.com.au>
 */
trait NullableFields
{
    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    abstract public function getAttributes();


    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    abstract public function getAttribute($key);


    /**
     * Determine whether a value is JSON castable for inbound manipulation.
     *
     * @param  string  $key
     *
     * @return bool
     */
    abstract protected function isJsonCastable($key);


    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    abstract public function hasSetMutator($key);


    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    abstract public function getDates();


    /**
     * Boot the trait, add a saving observer.
     *
     * When saving the model, we iterate over its attributes and for any attribute
     * marked as nullable whose value is empty, we then set its value to null.
     */
    protected static function bootNullableFields()
    {
        static::saving(function ($model) {
            $model->setNullableFields();
        });
    }


    /**
     * Set empty nullable fields to null.
     *
     * @since  1.1.0
     *
     * @return void
     */
    protected function setNullableFields()
    {
        foreach ($this->nullableFromArray($this->getAttributes()) as $key => $value) {
            $this->attributes[$key] = $this->nullIfEmpty($value, $key);
        }
    }


    /**
     * If value is empty, return null, otherwise return the original input.
     *
     * @param  string $value
     * @param  null $key
     *
     * @return null|string
     */
    public function nullIfEmpty($value, $key = null)
    {
        if (! is_null($key)) {
            $value = $this->fetchValueForKey($key, $value);
        }

        if (is_array($value)) {
            return $this->nullIfEmptyArray($key, $value);
        }

        if (is_bool($value)) {
            return $value;
        }

        return trim($value) === '' ? null : $value;
    }


    /**
     * Get the nullable attributes of a given array.
     *
     * @param  array $attributes
     *
     * @return array
     */
    protected function nullableFromArray(array $attributes = [])
    {
        if (is_array($this->nullable) && count($this->nullable) > 0) {
            return array_intersect_key($attributes, array_flip($this->nullable));
        }

        // Assume no fields are nullable
        return [];
    }


    /**
     * Return value of the native PHP type as a json-encoded value
     *
     * @param  mixed $value
     *
     * @return string
     */
    private function setJsonCastValue($value)
    {
        return method_exists($this, 'asJson') ? $this->asJson($value) : json_encode($value);
    }


    /**
     * Return value of the json-encoded value as a native PHP type
     *
     * @param  mixed $value
     *
     * @return string
     */
    private function getJsonCastValue($value)
    {
        return method_exists($this, 'fromJson') ? $this->fromJson($value) : json_decode($value, true);
    }


    /**
     * For the given key and value pair, determine the actual value,
     * depending on whether or not a mutator or cast is in use.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function fetchValueForKey($key, $value)
    {
        if (in_array($key, $this->getDates())) {
            return trim($value) === '' ? null : $value;
        }

        if (! $this->hasSetMutator($key)) {
            $value = $this->getAttribute($key);
        }

        if ($this->isJsonCastable($key) && ! is_null($value)) {
            $value = is_string($value) ? $this->getJsonCastValue($value) : $value;
        }

        return $value;
    }


    /**
     * Determine whether an array value is empty, taking into account casting.
     *
     * @param  string  $key
     * @param  array  $value
     *
     * @return mixed
     */
    private function nullIfEmptyArray($key, $value)
    {
        if ($this->isJsonCastable($key) && ! empty($value)) {
            return $this->setJsonCastValue($value);
        }

        return empty($value) ? null : $value;
    }
}
