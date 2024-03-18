<?php

namespace Tests\Fakes;

class UserProfileAll extends UserProfile
{
    public $table = 'user_profiles';

    protected $nullable = '*';
}
