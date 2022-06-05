<?php

namespace Owlcoder\Taxonomy;

use Illuminate\Database\Eloquent\Relations\Relation;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/taxonomy.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('taxonomy.php'),
        ], 'config');

        Relation::enforceMorphMap([
            'Term' => 'App\Models\Term',
            'Taxonomy' => 'App\Models\Taxonomy',
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'taxonomy'
        );

        $this->app->bind('taxonomy', function () {
            return new Taxonomy();
        });

        \View::addLocation(__DIR__ . '/../resources/views');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
