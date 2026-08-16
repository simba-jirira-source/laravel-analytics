<?php

declare(strict_types=1);

use Illuminate\Contracts\View\Factory;
use Throwable;

if (! function_exists('app')) {
    return;
}

try {
    if (! app()->bound('view')) {
        return;
    }

    /** @var Factory $viewFactory */
    $viewFactory = app('view');
    $finder = $viewFactory->getFinder();
    $hints = $finder->getHints();

    if (isset($hints['analytics'])) {
        return;
    }

    $viewPath = dirname(__DIR__).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views';

    if (! is_dir($viewPath)) {
        return;
    }

    $finder->addNamespace('analytics', $viewPath);
} catch (Throwable) {
    //
}
