<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use InvalidArgumentException;

class IpAddressValidator
{
    public function __construct(
        protected IpAddressNormalizer $normalizer,
    ) {}

    public function validate(string $ip): string
    {
        $ip = trim($ip);

        if ($ip === '') {
            throw new InvalidArgumentException('An IP address is required.');
        }

        if (str_contains($ip, '/')) {
            throw new InvalidArgumentException('CIDR ranges are not supported.');
        }

        $normalized = $this->normalizer->normalize($ip);

        if ($normalized === '' || ! $this->isValidNormalized($normalized)) {
            throw new InvalidArgumentException("Invalid IP address: {$ip}");
        }

        return $normalized;
    }

    public function isValid(string $ip): bool
    {
        try {
            $this->validate($ip);

            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    protected function isValidNormalized(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false;
    }
}
