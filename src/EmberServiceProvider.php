<?php

namespace Bizhub\Ember;

use Illuminate\Support\ServiceProvider;

class EmberServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/ember.php', 'ember');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/ember.php' => config_path('ember.php'),
        ], 'config');
    }
}
