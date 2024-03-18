<?php

namespace Tests\Fakes;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model;

class DateTest extends Model
{
    use NullableFields;

    public $timestamps = false;

    protected $table = 'dates';

    protected $fillable = ['last_tested_at'];

    protected $dates = ['last_tested_at'];

    protected $nullable = ['last_tested_at'];
}
