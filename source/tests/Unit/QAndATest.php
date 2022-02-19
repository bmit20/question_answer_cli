<?php

namespace Tests\Unit;

use Tests\TestCase;

class QAndATest extends TestCase
{
    /** @test */
    public function it_has_qanda_interactive_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\QAndA::class));
    }
    /** @test */
    public function it_has_qanda_reset_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\QAndAReset::class));
    }
    /** @test */
    public function test_console_command()
    {
        $this->artisan('qanda:reset')
            ->expectsOutput('All previous progresses has been removed')
            ->assertExitCode(0);
    }

}
