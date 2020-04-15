<?php
namespace plokko\ResourceQuery;

use App\Wip\ControllerConfig;
use Illuminate\Support\ServiceProvider;

class AutoControllerServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        // Publish default config //
        $this->publishes([
            __DIR__.'/config/default.php' => config_path('AutoController.php'),
        ]);
        $this->loadViewsFrom(__DIR__.'/views', 'autocontroller');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/default.php','AutoController');

        /*
        $this->app->singleton('ControllerConfig',function ($app){
            return new ControllerConfig(config('resourcequery'));//config('page.config')
        });
        //*/
    }

    public function provides()
    {
        return [];
    }
}
