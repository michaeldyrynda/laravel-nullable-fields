<?php

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Tests\Fakes\DateTest;
use Tests\Fakes\Product;
use Tests\Fakes\UserProfile;
use Tests\Fakes\UserProfileAll;

beforeAll(function () {
    $manager = new Manager();
    $manager->addConnection([
        'driver' => 'sqlite',
        'database' => ':memory:',
    ]);

    $manager->setEventDispatcher(new Dispatcher(new Container()));

    $manager->setAsGlobal();
    $manager->bootEloquent();

    $manager->schema()->create('user_profiles', function ($table) {
        $table->increments('id');
        $table->string('facebook_profile')->nullable()->default(null);
        $table->string('twitter_profile')->nullable()->default(null);
        $table->string('twitter_profile_mutated')->nullable()->default(null);
        $table->string('linkedin_profile')->nullable()->default(null);
        $table->text('array_casted')->nullable()->default(null);
        $table->text('array_not_casted')->nullable()->default(null);
        $table->boolean('boolean')->nullable()->default(null);
    });

    $manager->schema()->create('products', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->string('amount')->nullable()->default(null);
    });

    $manager->schema()->create('dates', function ($table) {
        $table->increments('id');
        $table->timestamp('last_tested_at')->nullable()->default(null);
    });
});

it('sets nullable fields to null when saving', function () {
    $user = new UserProfile();
    $user->facebook_profile = ' ';
    $user->twitter_profile = 'michaeldyrynda';
    $user->linkedin_profile = '';
    $user->array_casted = [];
    $user->array_not_casted = [];
    $user->save();

    expect($user)
        ->facebook_profile->toBeNull()
        ->twitter_profile->toBe('michaeldyrynda')
        ->linkedin_profile->toBeNull()
        ->array_casted->toBeNull()
        ->array_not_casted->toBeNull();
});
it('sets nullable fields to null when mass assignment is used', function () {
    $user = UserProfile::create([
        'facebook_profile' => '',
        'twitter_profile' => 'michaeldyrynda',
        'linkedin_profile' => ' ',
        'array_casted' => [],
        'array_not_casted' => [],
    ]);

    expect($user)
        ->facebook_profile->toBeNull()
        ->twitter_profile->toBe('michaeldyrynda')
        ->linkedin_profile->toBeNull()
        ->array_casted->toBeNull()
        ->array_not_casted->toBeNull();
});

it('sets all empty fields to null when mass assignment is used', function () {
    $user = UserProfileAll::create([
        'facebook_profile' => '',
        'twitter_profile' => 'michaeldyrynda',
        'linkedin_profile' => ' ',
        'array_casted' => [],
        'array_not_casted' => [],
    ]);

    expect($user)
        ->facebook_profile->toBeNull()
        ->twitter_profile->toBe('michaeldyrynda')
        ->linkedin_profile->toBeNull()
        ->array_casted->toBeNull()
        ->array_not_casted->toBeNull();
});

it('handles calling the nullabe fields setter manually', function () {
    $user = UserProfile::create([
        'facebook_profile' => '',
        'twitter_profile' => '',
        'linkedin_profile' => '',
    ]);

    expect($user)
        ->facebook_profile->toBeNull()
        ->twitter_profile->toBeNull()
        ->linkedin_profile->toBeNull();
});

it('handles a cast value with a mutator set', function () {
    $product = Product::create(['name' => "mikemand's test product", 'amount' => 6.27]);

    expect($product->amount)->not->toBeNull();
});

it('handles an empty cast value with a mutator set', function () {
    $product = Product::create(['name' => "mikemand's test product"]);

    expect($product)->amount->toBeNull();
});

it('handles setting a cast value to an empty value via a mutator', function () {
    $product = Product::create(['name' => "mikemand's test product", 'amount' => '6.27']);

    expect($product)->amount->not->toBeNull();

    $product->someCondition = true;
    $product->update(['amount' => 'this will be an empty array']);

    expect($product)->amount->toBeNull();
});

it('doesnt munge an existing non null value on save', function () {
    $profile = UserProfile::create(['twitter_profile' => 'michaeldyrynda']);

    expect($profile->twitter_profile)->toBe('michaeldyrynda');

    $profile->save();

    expect($profile)->twitter_profile->toBe('michaeldyrynda');
});

it('doesnt munge an existing non null value with a mutator set on save', function () {
    $profile = UserProfile::create(['twitter_profile_mutated' => 'michaeldyrynda']);

    expect($profile)->twitter_profile_mutated->toBe('@michaeldyrynda');

    $profile->save();

    expect($profile)->twitter_profile_mutated->toBe('@michaeldyrynda');
});

it('doesnt munge a boolean false value', function () {
    $user = UserProfile::create([
        'facebook_profile' => '',
        'boolean' => false,
    ]);

    expect($user)
        ->facebook_profile->toBeNull()
        ->boolean->toBeFalse();
});

it('correctly handles empty date fields', function () {
    $date = DateTest::create(['last_tested_at' => '']);

    expect($date)->last_tested_at->toBeNull();
});

it('correctly handles set date fields', function () {
    $date = DateTest::create(['last_tested_at' => Carbon::parse('2016-12-22 09:12:00')]);

    expect((string) $date->last_tested_at)->toBe('2016-12-22 09:12:00');
});
