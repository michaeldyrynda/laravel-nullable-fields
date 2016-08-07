<?php

use Iatstuti\Database\Support\NullableFields;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;

class NullableFieldsIntegrationTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        $manager = new Manager();
        $manager->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        $manager->setEventDispatcher(new Dispatcher(new Container()));

        $manager->setAsGlobal();
        $manager->bootEloquent();

        $manager->schema()->create('user_profiles', function ($table) {
            $table->increments('id');
            $table->string('facebook_profile')->nullable()->default(null);
            $table->string('twitter_profile')->nullable()->default(null);
            $table->string('linkedin_profile')->nullable()->default(null);
            $table->text('array_casted')->nullable()->default(null);
            $table->text('array_not_casted')->nullable()->default(null);
        });

        $manager->schema()->create('products', function ($table) {
            $table->increments('id');
            $table->string('amount')->nullable()->default(null);
        });
    }


    /** @test */
    public function it_sets_nullable_fields_to_null_when_saving()
    {
        $user                   = new UserProfile;
        $user->facebook_profile = ' ';
        $user->twitter_profile  = 'michaeldyrynda';
        $user->linkedin_profile = '';
        $user->array_casted     = [];
        $user->array_not_casted = [];
        $user->save();

        $this->assertNull($user->facebook_profile);
        $this->assertSame('michaeldyrynda', $user->twitter_profile);
        $this->assertNull($user->linkedin_profile);
        $this->assertNull($user->array_casted);
        $this->assertNull($user->array_not_casted);
        $this->assertNull(null);
    }


    /** @test */
    public function it_sets_nullable_fields_to_null_when_mass_assignment_is_used()
    {
        $user = UserProfile::create([
            'facebook_profile' => '',
            'twitter_profile'  => 'michaeldyrynda',
            'linkedin_profile' => ' ',
            'array_casted'     => [],
            'array_not_casted' => [],
        ]);

        $this->assertNull($user->facebook_profile);
        $this->assertSame('michaeldyrynda', $user->twitter_profile);
        $this->assertNull($user->linkedin_profile);
        $this->assertNull($user->array_casted);
        $this->assertNull($user->array_not_casted);
    }


    /** @test */
    public function it_handles_calling_the_nullabe_fields_setter_manually()
    {
        $user = UserProfile::create([
            'facebook_profile' => '',
            'twitter_profile'  => '',
            'linkedin_profile' => '',
        ]);

        $this->assertNull($user->facebook_profile);
        $this->assertNull($user->twitter_profile);
        $this->assertNull($user->linkedin_profile);
    }

    /** @test */
    public function it_handles_a_json_cast_value_with_a_mutator_set()
    {
        $product = Product::create(['amount' => 6.27]);

        $this->assertNotNull($product->amount);
    }
}

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
    ];

    protected $nullable = [
        'facebook_profile',
        'twitter_profile',
        'linkedin_profile',
        'array_casted',
        'array_not_casted',
    ];

    protected $casts = ['array_casted' => 'array'];
}


class UserProfileSaving extends Model
{
    use NullableFields;

    protected $table = 'user_profiles';

    public $timestamps = false;

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

class Product extends Model
{
    protected $fillable = ['amount'];

    public $timestamps = false;

    protected $nullable = ['amount'];

    public function setAmountAttribute($amount)
    {
        $amount *= 100;

        $this->attributes['amount'] = ['amount' => $amount, 'currency' => 'USD'];
    }
}
