# Nullable database fields for the Laravel PHP Framework

[![Build Status](https://github.com/michaeldyrynda/laravel-nullable-fields/workflows/run-tests/badge.svg)](https://github.com/michaeldyrynda/laravel-nullable-fields/actions?query=workflow%3Arun-tests)
[![Latest Stable Version](https://poser.pugx.org/dyrynda/laravel-nullable-fields/v/stable)](https://packagist.org/packages/dyrynda/laravel-nullable-fields)
[![Total Downloads](https://poser.pugx.org/dyrynda/laravel-nullable-fields/downloads)](https://packagist.org/packages/dyrynda/laravel-nullable-fields)
[![License](https://poser.pugx.org/dyrynda/laravel-nullable-fields/license)](https://packagist.org/packages/dyrynda/laravel-nullable-fields)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/michaeldyrynda/laravel-nullable-fields)

Often times, database fields that are not assigned values are defaulted to `null`. This is particularly important when creating records with foreign key constraints, where the relationship is not yet established.

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
$ composer require dyrynda/laravel-nullable-fields
```

In order to use this trait, import it in your Eloquent model, then set the protected `$nullable` property as an array of fields you would like to be saved as `null` when empty.

```php
<?php

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\NullableFields;

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

## Treeware

You're free to use this package, but if it makes it to your production environment you are required to buy the world a tree.

It’s now common knowledge that one of the best tools to tackle the climate crisis and keep our temperatures from rising above 1.5C is to plant trees. If you support this package and contribute to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.

You can buy trees [here](https://plant.treeware.earth/michaeldyrynda/laravel-nullable-fields)

Read more about Treeware at [treeware.earth](https://treeware.earth)
