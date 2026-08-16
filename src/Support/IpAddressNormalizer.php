<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Support;

class IpAddressNormalizer
{
    public function normalize(?string $ip): string
    {
        if ($ip === null || $ip === '') {
            return '';
        }

        $ip = trim($ip);

        if (str_starts_with(strtolower($ip), '::ffff:')) {
            $mapped = substr($ip, 7);

            if (filter_var($mapped, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $mapped;
            }
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            $packed = inet_pton($ip);

            if ($packed !== false) {
                $normalized = inet_ntop($packed);

                if (is_string($normalized)) {
                    return strtolower($normalized);
                }
            }

            return strtolower($ip);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return $ip;
        }

        return $ip;
    }
}
