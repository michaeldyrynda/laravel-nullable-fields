<?php

namespace Tests\Fakes;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model;

class UserProfileSaving extends Model
{
    use NullableFields;

    public $timestamps = false;

    protected $table = 'user_profiles';

    protected $fillable = [
        'facebook_profile',
        'twitter_profile',
        'linkedin_profile',
        'array_casted',
        'array_not_casted',
    ];

    protected $nullable = [
        'facebook_profile',
        'twitter_profile',
        'linkedin_profile',
        'array_casted',
        'array_not_casted',
    ];

    public static function boot()
    {
        static::saving(function ($model) {
            // some other behaviour

            $model->setNullableFields();
        });
    }
}
