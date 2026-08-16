<?php

declare(strict_types=1);

/**
 * Pest type coverage uses pokio for process detection, which does not support Windows.
 * CI runs this gate on Ubuntu; local Windows runs skip with a successful exit code.
 */
if (PHP_OS_FAMILY === 'Windows') {
    fwrite(STDOUT, "Skipping type coverage on Windows (pokio does not support this OS). Run in CI on Ubuntu.\n");

    exit(0);
}

$command = 'vendor/bin/pest --type-coverage --min=100 --memory-limit=512M';

passthru($command, $exitCode);

exit($exitCode);
