<?php

namespace Bizhub\Ember\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Bizhub\Ember\EmberServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            EmberServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // $app['config']->set('ember.', 'test-key');
        // $app['config']->set('ember.', 'gpt-4');
    }
}
