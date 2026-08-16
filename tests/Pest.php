<?php

declare(strict_types=1);

use LaravelAnalytics\LaravelAnalytics\Tests\DashboardTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\DatabaseTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\DisabledDashboardTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\ErrorTrackingTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\IpBanningTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\MissingAuthorizationDashboardTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\RetentionTestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\TestCase;
use LaravelAnalytics\LaravelAnalytics\Tests\TrackingTestCase;

uses(TestCase::class)->in('Feature', 'Unit');

uses(DatabaseTestCase::class)->in('Database');

uses(TrackingTestCase::class)->in('Tracking');

uses(ErrorTrackingTestCase::class)->in('ErrorTracking');

uses(IpBanningTestCase::class)->in('IpBanning');

uses(RetentionTestCase::class)->in('Retention');

uses(DashboardTestCase::class)->in('Dashboard/DashboardAccessTest.php', 'Dashboard/DashboardLivewireTest.php');

uses(DisabledDashboardTestCase::class)->in('Dashboard/DashboardRouteRegistrationDisabledTest.php');

uses(MissingAuthorizationDashboardTestCase::class)->in('Dashboard/DashboardRouteRegistrationMissingAuthorizationTest.php');

function runtimeExceptionWithMessage(string $message): RuntimeException
{
    return new RuntimeException($message);
}
