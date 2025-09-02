<?php

namespace Bizhub\Ember\Tests\Feature;

use Bizhub\Ember\Ember;
use Bizhub\Ember\Tests\TestCase;

class EmberTest extends TestCase
{
    public function test_memory_consume_and_summarize()
    {
        $summary = Ember::memory()
            ->consume("This is a test document.")
            ->summarize();

        $this->assertStringContainsString('Summary', $summary);
    }
}
