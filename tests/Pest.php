<?php

declare(strict_types=1);

use SimbaJirira\LaravelAnalytics\Tests\DashboardTestCase;
use SimbaJirira\LaravelAnalytics\Tests\DatabaseIntegrationTestCase;
use SimbaJirira\LaravelAnalytics\Tests\DatabaseTestCase;
use SimbaJirira\LaravelAnalytics\Tests\DisabledDashboardTestCase;
use SimbaJirira\LaravelAnalytics\Tests\ErrorTrackingTestCase;
use SimbaJirira\LaravelAnalytics\Tests\IpBanningTestCase;
use SimbaJirira\LaravelAnalytics\Tests\MissingAuthorizationDashboardTestCase;
use SimbaJirira\LaravelAnalytics\Tests\RetentionTestCase;
use SimbaJirira\LaravelAnalytics\Tests\TestCase;
use SimbaJirira\LaravelAnalytics\Tests\TrackingTestCase;

uses(TestCase::class)->in('Feature', 'Unit');

uses(DatabaseTestCase::class)->in('Database');

uses(DatabaseIntegrationTestCase::class)->in('DatabaseIntegration');

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
