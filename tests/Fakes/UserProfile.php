<?php

namespace Tests\Fakes;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use NullableFields;

    public $timestamps = false;

    protected $fillable = [
        'facebook_profile',
        'twitter_profile',
        'linkedin_profile',
        'array_casted',
        'array_not_casted',
        'twitter_profile_mutated',
        'boolean',
    ];

    protected $nullable = [
        'facebook_profile',
        'twitter_profile',
        'linkedin_profile',
        'array_casted',
        'array_not_casted',
        'twitter_profile_mutated',
        'boolean',
    ];

    protected $casts = ['array_casted' => 'array', 'boolean' => 'boolean'];

    public function setTwitterProfileMutatedAttribute($twitter_profile_mutated)
    {
        $this->attributes['twitter_profile_mutated'] = sprintf('@%s', $twitter_profile_mutated);
    }
}
