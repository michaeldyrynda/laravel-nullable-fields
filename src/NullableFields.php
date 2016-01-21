<?php

namespace Iatstuti\Database\Support;

/**
 * Nullable (database) fields trait.
 *
 * Include this trait in any Eloquent models you wish to automatically set
 * empty field values to null on. When saving, iterate over the model's
 * attributes and if their value is empty, make it null before save.
 *
 * @package    Iatstuti
 * @subpackage Database\Support
 * @copyright  2015 IATSTUTI
 * @author     Michael Dyrynda <michael@iatstuti.net>
 */
trait NullableFields
{

    /**
     * Boot the trait, add a saving observer.
     *
     * When saving the model, we iterate over its attributes and for any attribute
     * marked as nullable whose value is empty, we then set its value to null.
     */
    protected static function bootNullableFields()
    {
        static::saving(function ($model) {
            foreach ($model->nullableFromArray($model->getAttributes()) as $key => $value) {
                $model->attributes[$key] = $model->nullIfEmpty($value, $key);
            }
        });
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
        if (! is_null($key) && $this->isJsonCastable($key)) {
            return empty($this->fromJson($value)) ? null : $value;
        }

        if (is_array($value)) {
            return empty($value) ? null : $value;
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
    protected function nullableFromArray(array $attributes = [ ])
    {
        if (is_array($this->nullable) && count($this->nullable) > 0) {
            return array_intersect_key($attributes, array_flip($this->nullable));
        }

        // Assume no fields are nullable
        return [ ];
    }
}
