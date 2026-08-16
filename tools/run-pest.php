<?php

declare(strict_types=1);

/**
 * Run Pest with parallel workers on Unix-like systems.
 * Windows Testbench bootstrap cache locking fails under Pest parallel.
 */
$arguments = array_slice($argv, 1);

$command = ['vendor/bin/pest'];

if (PHP_OS_FAMILY !== 'Windows') {
    $command[] = '--parallel';
}

array_push($command, ...$arguments);

$escaped = implode(' ', array_map(static fn (string $part): string => escapeshellarg($part), $command));

passthru($escaped, $exitCode);

exit($exitCode);
