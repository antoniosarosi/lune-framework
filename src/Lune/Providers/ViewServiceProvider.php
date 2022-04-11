<?php

namespace Lune\Providers;

use Lune\View\LuneEngine;
use Lune\View\View;

class ViewServiceProvider {
    public function registerServices() {
        match (config("view.engine", "lune")) {
            "lune" => singleton(View::class, fn () => new LuneEngine(config("view.path"))),
        };
    }
}
