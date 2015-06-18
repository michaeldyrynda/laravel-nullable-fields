# Nullable database fields for the Laravel PHP Framework

Often times, database fields that are not assigned values are defaulted to `null`. This is particularly important when creating records with foreign key constraints.

Note, the database field must be configured to allow null.

More recent versions of MySQL will convert the value to an empty string if the field is not configured to allow null. Be aware that older versions may actually return an error.

Laravel (5.1) does not currently support automatically setting nullable database fields as `null` when the value assigned to a given attribute is empty.

# Installation

This trait is installed via [Composer](http://getcomposer.org/). To install, simply add it to your `composer.json` file:

```
{
	"require": {
		"iatstuti/laravel-nullable-fields": "~0.1"
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
	];
	
}
```

Now, any time you are saving a `UserProfile` profile instance, any empty attributes that are set in the `$nullable` property will be saved as `null`.

```php
<?php

$profile = new UserProfile::find(1);
$profile->facebook_profile = ' '; // Empty, saved as null
$profile->twitter_profile  = 'michaeldyrynda';
$profile->linkedin_profile = '';  // Empty, saved as null
$profile->save();
```

# More information

[Working with nullable fields in Eloquent models](https://iatstuti.net/blog/working-with-nullable-fields-in-eloquent-models) - first iteration

[Working with nullable fields in Eloquent models - Part Deux](https://iatstuti.net/blog/working-with-nullable-field-in-eloquent-models-part-deux) - second iteration, covers the details of this package

# Support

If you are having general issues with this package, feel free to contact me on [Twitter](https://twitter.com/michaeldyrynda).

If you believe you have found an issue, please report it using the [GitHub issue tracker](https://github.com/deringer/laravel-nullable-fields/issues), or better yet, for the repository and submit a pull request.

If you're using this package, I'd love to hear your thoughts. Thanks!
