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
    public function it_handles_a_cast_value_with_a_mutator_set()
    {
        $product = Product::create(['name' => "mikemand's test product", 'amount' => 6.27]);

        $this->assertNotNull($product->amount);
    }

    /** @test */
    public function it_handles_an_empty_cast_value_with_a_mutator_set()
    {
        $product = Product::create(['name' => "mikemand's test product"]);

        $this->assertNull($product->amount);
    }

    /** @test */
    public function it_handles_setting_a_cast_value_to_an_empty_value_via_a_mutator()
    {
        $product = Product::create(['name' => "mikemand's test product", 'amount' => '6.27']);

        $this->assertNotNull($product->amount);

        $product->someCondition = true;
        $product->update(['amount' => 'this will be an empty array']);

        $this->assertNull($product->amount);
    }

    /** @test */
    public function it_doesnt_munge_an_existing_non_null_value_on_save()
    {
        $profile = UserProfile::create(['twitter_profile' => 'michaeldyrynda']);

        $this->assertEquals('michaeldyrynda', $profile->twitter_profile);

        $profile->save();

        $this->assertEquals('michaeldyrynda', $profile->twitter_profile);
    }

    /** @test */
    public function it_doesnt_munge_an_existing_non_null_value_with_a_mutator_set_on_save()
    {
        $profile = UserProfile::create(['twitter_profile_mutated' => 'michaeldyrynda']);

        $this->assertEquals('@michaeldyrynda', $profile->twitter_profile_mutated);

        $profile->save();

        $this->assertEquals('@michaeldyrynda', $profile->twitter_profile_mutated);
    }

    /** @test */
    public function it_doesnt_munge_a_boolean_false_value()
    {
        $user = UserProfile::create([
            'facebook_profile' => '',
            'boolean' => false,
        ]);

        $this->assertNull($user->facebook_profile);
        $this->assertFalse($user->boolean);
    }

    /** @test */
    public function it_correctly_handles_empty_date_fields()
    {
        $date = DateTest::create(['last_tested_at' => '']);

        $this->assertNull($date->last_tested_at);        
    }

    /** @test */
    public function it_correctly_handles_set_date_fields()
    {
        $date = DateTest::create(['last_tested_at' => Carbon\Carbon::parse('2016-12-22 09:12:00')]);

        $this->assertEquals('2016-12-22 09:12:00', (string) $date->last_tested_at);
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
    use NullableFields;

    protected $fillable = ['name', 'amount'];

    public $timestamps = false;

    protected $nullable = ['amount'];

    protected $casts = ['amount' => 'array'];

    public $someCondition = false;

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

class DateTest extends Model
{
    use NullableFields;

    protected $table = 'dates';

    protected $fillable = ['last_tested_at'];

    protected $dates = ['last_tested_at'];

    protected $nullable = ['last_tested_at'];

    public $timestamps = false;
}
