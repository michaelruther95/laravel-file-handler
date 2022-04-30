<?php

namespace Michaelruther95\LaravelFileHandler;

use Illuminate\Support\ServiceProvider;

class MainServiceProvider extends ServiceProvider {

    public function boot () {
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
    }

    public function register () {
    
    }

}