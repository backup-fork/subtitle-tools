<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\UniqueEmailRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniqueEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_prevents_the_same_email_from_registering_twice()
    {
        $this->assertUniqueEmailPasses('test@example.com');

        $this->createUser(['email' => 'test@example.com']);

        $this->assertUniqueEmailFails('test@example.com');
    }

    /** @test */
    function it_is_case_insensitive()
    {
        $this->createUser(['email' => 'test@example.com']);

        $this->assertUniqueEmailFails('TEST@example.com');
    }

    /** @test */
    function it_ignores_plus_signs()
    {
        $this->assertUniqueEmailPasses('test+1@example.com');

        $this->createUser(['email' => 'test@example.com']);

        $this->assertUniqueEmailFails('test+1@example.com');

        $this->createUser(['email' => 'another+test+1@example.com']);

        $this->assertUniqueEmailFails('another@example.com');
    }

    /** @test */
    function it_ignores_dots()
    {
        $this->assertUniqueEmailPasses('te.st@example.com');

        $this->createUser(['email' => 'test@example.com']);

        $this->assertUniqueEmailFails('te.st@example.com');

        $this->createUser(['email' => 'an.other@example.com']);

        $this->assertUniqueEmailFails('another@example.com');
    }

    private function assertUniqueEmailPasses($value)
    {
        $this->assertTrue(
            (new UniqueEmailRule)->passes('email', $value)
        );

        return $this;
    }

    private function assertUniqueEmailFails($value)
    {
        $this->assertFalse(
            (new UniqueEmailRule)->passes('email', $value)
        );

        return $this;
    }
}
