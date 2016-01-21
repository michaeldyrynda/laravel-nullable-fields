<?php

use Iatstuti\Database\Support\NullableFields;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;

class NullableFieldsIntegrationTest extends PHPUnit_Framework_TestCase
{

    protected static $dbname;


    public static function setUpBeforeClass()
    {
        static::$dbname = dirname(__FILE__) . '/database.sqlite';

        touch(static::$dbname);

        $manager = new Manager();
        $manager->addConnection([
            'driver'   => 'sqlite',
            'database' => static::$dbname,
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
    }
    
    
    /** @test */
    public function it_sets_nullable_fields_to_null_when_saving()
    {
        $user = new UserProfile;
        $user->facebook_profile = ' ';
        $user->twitter_profile  = 'michaeldyrynda';
        $user->linkedin_profile = '';
        $user->array_casted = [ ];
        $user->array_not_casted = [ ];
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
            'array_casted'     => [ ],
            'array_not_casted' => [ ],
        ]);

        $this->assertNull($user->facebook_profile);
        $this->assertSame('michaeldyrynda', $user->twitter_profile);
        $this->assertNull($user->linkedin_profile);
        $this->assertNull($user->array_casted);
        $this->assertNull($user->array_not_casted);
    }


    public static function tearDownAfterClass()
    {
        unlink(static::$dbname);
    }
}


class UserProfile extends Model
{
    use NullableFields;

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

    protected $casts = [ 'array_casted' => 'array', ];

    public $timestamps = false;

}
