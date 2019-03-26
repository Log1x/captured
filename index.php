<?php

namespace Log1x\Captured;

use Log1x\Captured\Captured;

if (class_exists('Log1x\\Captured\\Captured') || ! file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    return;
}

require_once $composer;

new Captured();
