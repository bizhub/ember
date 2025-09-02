<?php

namespace Bizhub\Ember\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Bizhub\Ember\Providers\EmberServiceProvider;

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
        $app['config']->set('ember.prism_api_key', 'test-key');
        $app['config']->set('ember.default_model', 'gpt-4');
    }
}
