<?php

use PHPUnit\Framework\TestCase;

class NullableFieldsTest extends TestCase
{

    /**
     * Setup the test, getting an object for the NullableFields trait.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->nullable = $this->getMockForTrait('Dyrynda\Database\Support\NullableFields');
    }


    /** @test */
    public function it_identifies_an_empty_string_as_null()
    {
        $this->assertNull($this->nullable->nullIfEmpty(''));
    }


    /** @test */
    public function it_identifies_a_string_with_whitespace_only_as_null()
    {
        $this->assertNull($this->nullable->nullIfEmpty(' '));
    }


    /** @test */
    public function it_does_not_modify_a_non_empty_value()
    {
        $this->assertSame('michaeldyrynda', $this->nullable->nullIfEmpty('michaeldyrynda'));
    }


    /** @test */
    public function it_does_not_modify_a_non_empty_value_surrounded_by_whitespace()
    {
        $this->assertSame(' michaeldyrynda ', $this->nullable->nullIfEmpty(' michaeldyrynda '));
    }


    /** @test */
    public function it_does_not_modify_a_false_value()
    {
        $this->assertFalse($this->nullable->nullIfEmpty(false));
    }


    /** @test */
    public function it_correctly_handles_an_empty_array_as_null()
    {
        $this->assertNull($this->nullable->nullIfEmpty([]));
    }


    /** @test */
    public function it_correctly_handles_a_non_empty_array()
    {
        $this->assertSame(['twitter' => 'michaeldyrynda'], $this->nullable->nullIfEmpty(['twitter' => 'michaeldyrynda']));
    }
}
