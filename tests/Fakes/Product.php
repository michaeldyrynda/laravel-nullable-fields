<?php

namespace Tests\Fakes;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use NullableFields;

    public $timestamps = false;

    public $someCondition = false;

    protected $fillable = ['name', 'amount'];

    protected $nullable = ['amount'];

    protected $casts = ['amount' => 'array'];

    public function setAmountAttribute($amount)
    {
        if ($this->someCondition) {
            $this->attributes['amount'] = [];
        } else {
            $amount *= 100;

            $this->attributes['amount'] = json_encode(['amount' => $amount, 'currency' => 'USD']);
        }
    }
}
