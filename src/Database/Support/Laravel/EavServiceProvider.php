<?php

namespace Drobotik\Eav\Database\Support\Laravel;

use Illuminate\Support\ServiceProvider;

class EavServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/migrations/' => database_path('migrations/'),
        ], 'eav');
    }
}
