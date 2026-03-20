<?php

/**
 * Setup the test, getting an object for the NullableFields trait.
 *
 * @return void
 */

use Dyrynda\Database\Support\NullableFields;

beforeEach(function () {
    $this->nullable = new class
    {
        use NullableFields;

        public function getAttributes(): array
        {
            return [];
        }

        public function getAttribute($key): mixed
        {
            return null;
        }

        public function hasSetMutator($key): bool
        {
            return false;
        }

        public function getDates(): array
        {
            return [];
        }

        protected function isJsonCastable($key): bool
        {
            return false;
        }
    };
});

it('identifies an empty string as null', function () {
    expect($this->nullable)->nullIfEmpty('')->toBeNull();
});

it('identifies a string with whitespace only as null', function () {
    expect($this->nullable)->nullIfEmpty(' ')->toBeNull();
});

it('does not modify a non empty value', function () {
    expect($this->nullable)->nullIfEmpty('michaeldyrynda')->toBe('michaeldyrynda');
});

it('does not modify a non empty value surrounded by whitespace', function () {
    expect($this->nullable)->nullIfEmpty(' michaeldyrynda ')->toBe(' michaeldyrynda ');
});

it('does not modify a false value', function () {
    expect($this->nullable)->nullIfEmpty(false)->toBeFalse();
});

it('correctly handles an empty array as null', function () {
    expect($this->nullable)->nullIfEmpty([])->toBeNull();
});

it('correctly handles a non empty array', function () {
    expect($this->nullable)->nullIfEmpty(['twitter' => 'michaeldyrynda'])->toBe(['twitter' => 'michaeldyrynda']);
});
