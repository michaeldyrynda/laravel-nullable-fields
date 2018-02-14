# Nullable database fields for the Laravel PHP Framework
## v1.6.0

![Travis Build Status](https://travis-ci.org/michaeldyrynda/laravel-nullable-fields.svg?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/michaeldyrynda/laravel-nullable-fields/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/michaeldyrynda/laravel-nullable-fields/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/michaeldyrynda/laravel-nullable-fields/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/michaeldyrynda/laravel-nullable-fields/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/iatstuti/laravel-nullable-fields/v/stable)](https://packagist.org/packages/iatstuti/laravel-nullable-fields)
[![Total Downloads](https://poser.pugx.org/iatstuti/laravel-nullable-fields/downloads)](https://packagist.org/packages/iatstuti/laravel-nullable-fields)
[![License](https://poser.pugx.org/iatstuti/laravel-nullable-fields/license)](https://packagist.org/packages/iatstuti/laravel-nullable-fields)

Often times, database fields that are not assigned values are defaulted to `null`. This is particularly important when creating records with foreign key constraints, where the relationship is not yet established.

As of version 1.0, this package also supports converting empty arrays to `null` in fields that are cast to an array, or not.

As of 1.1.0, this package exposes the underlying functionality which determines and sets empty fields to `null` as a public method. This allows users to implement their own model `saving` event listeners, by calling the `setNullableFields` method along with any additional save-time behaviours.

As of version 1.2.0, this package handles attributes that have both casting and a mutator set. When using a mutator, ensure that you set a string for any non-empty values, and an empty (string, array, null, etc.) for any other case.

Version 1.3.0 adds compatibility for Laravel 5.3.

Version 1.3.2 adds better handling of `boolean` fields, particularly when set to `false`.

Version 1.4.0 adds compatibility for Laravel 5.4.

Version 1.5.0 adds compatibility for Laravel 5.5.

Version 1.6.0 adds compatibility for Laravel 5.6.

Note, the database field must be configured to allow null.

```php
public function up()
{
    Schema::create('profile_user', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->nullable()->default(null);
        $table->foreign('user_id')->references('users')->on('id'); 
        $table->string('twitter_profile')->nullable()->default(null);
        $table->string('facebook_profile')->nullable()->default(null);
        $table->string('linkedin_profile')->nullable()->default(null);
        $table->text('array_casted')->nullable()->default(null);
        $table->text('array_not_casted')->nullable()->default(null);
    });
}
```
    

More recent versions of MySQL will convert the value to an empty string if the field is not configured to allow null. Be aware that older versions may actually return an error.

Laravel does not currently support automatically setting nullable database fields as `null` when the value assigned to a given attribute is empty.

# Installation

This trait is installed via [Composer](http://getcomposer.org/). To install, simply add it to your `composer.json` file:

```
{
	"require": {
		"iatstuti/laravel-nullable-fields": "~1.0"
	}
}
```

Then run composer to update your dependencies:

```
$ composer update
```

In order to use this trait, import it in your Eloquent model, then set the protected `$nullable` property as an array of fields you would like to be saved as `null` when empty.

```php
<?php

use Iatstuti\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
	use NullableFields;
	
	protected $nullable = [
		'facebook_profile',
		'twitter_profile',
		'linkedin_profile',
		'array_casted',
		'array_not_casted',
	];
	
	protected $casts = [ 'array_casted' => 'array', ];
	
}
```

Now, any time you are saving a `UserProfile` profile instance, any empty attributes that are set in the `$nullable` property will be saved as `null`.

```php
<?php

$profile = new UserProfile::find(1);
$profile->facebook_profile = ' '; // Empty, saved as null
$profile->twitter_profile  = 'michaeldyrynda';
$profile->linkedin_profile = '';  // Empty, saved as null
$profile->array_casted = []; // Empty, saved as null
$profile->array_not_casted = []; // Empty, saved as null
$profile->save();
```

# More information

[Working with nullable fields in Eloquent models](https://dyrynda.com.au/blog/working-with-nullable-fields-in-eloquent-models) - first iteration

[Working with nullable fields in Eloquent models - Part Deux](https://dyrynda.com.au/blog/working-with-nullable-field-in-eloquent-models-part-deux) - second iteration, covers the details of this package

# Support

If you are having general issues with this package, feel free to contact me on [Twitter](https://twitter.com/michaeldyrynda).

If you believe you have found an issue, please report it using the [GitHub issue tracker](https://github.com/michaeldyrynda/laravel-nullable-fields/issues), or better yet, fork the repository and submit a pull request.

If you're using this package, I'd love to hear your thoughts. Thanks!
