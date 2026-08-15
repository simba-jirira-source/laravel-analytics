<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Tests\DatabaseTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\TestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\TrackingTestCase;

uses(TestCase::class)->in('Feature', 'Unit');

uses(DatabaseTestCase::class)->in('Database');

uses(TrackingTestCase::class)->in('Tracking');
