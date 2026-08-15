<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LaravelAnalytics\LaravelAnalytics\LaravelAnalytics
 */
class LaravelAnalytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LaravelAnalytics\LaravelAnalytics\LaravelAnalytics::class;
    }
}
