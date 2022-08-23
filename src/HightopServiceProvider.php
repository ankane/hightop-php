<?php

namespace Hightop;

use Illuminate\Support\ServiceProvider;

class HightopServiceProvider extends ServiceProvider
{
    public function register()
    {
        // do nothing
    }

    public function boot()
    {
        Builder::register();
    }
}
