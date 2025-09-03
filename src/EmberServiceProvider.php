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

        $this->publishes([
            __DIR__ . '/../database/migrations/create_ember_conversation_messages_table.php.stub' 
                => database_path('migrations/' . date('Y_m_d_His') . '_create_ember_conversation_messages_table.php'),
        ], 'migrations');
    }
}
