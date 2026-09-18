<?php

namespace App\Services\Network;

final class NetworkToolService
{
    public function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    public function ipClassification(string $ip): string
    {
        if (! $this->isValidIp($ip)) {
            return 'invalid';
        }

        $privateRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '127.0.0.0/8',
            '169.254.0.0/16',
            'fc00::/7',
            '::1/128',
            'fe80::/10',
        ];

        $ipBinary = inet_pton($ip);

        foreach ($privateRanges as $range) {
            [$network, $prefix] = explode('/', $range, 2);
            $networkBinary = inet_pton($network);

            if ($networkBinary === false || $ipBinary === false) {
                continue;
            }

            if (strlen($networkBinary) !== strlen($ipBinary)) {
                continue;
            }

            $mask = $this->cidrMask($prefix, strlen($ipBinary) * 8);

            if (($ipBinary & $mask) === ($networkBinary & $mask)) {
                return 'private';
            }
        }

        return 'public';
    }

    private function cidrMask(string $prefix, int $bits): string
    {
        $maskBits = (int) $prefix;
        $mask = str_repeat('1', $maskBits) . str_repeat('0', $bits - $maskBits);

        return pack('H*', str_pad(base_convert($mask, 2, 16), (int) ceil($bits / 8) * 2, '0', STR_PAD_LEFT));
    }
}
