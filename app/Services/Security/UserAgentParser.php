<?php

declare(strict_types=1);

namespace App\Services\Security;

final class UserAgentParser
{
    /**
     * @return array{browser: string, device: string, os: string}
     */
    public function parse(?string $userAgent): array
    {
        $ua = $userAgent ?? '';

        return [
            'browser' => $this->detectBrowser($ua),
            'device' => $this->detectDevice($ua),
            'os' => $this->detectOs($ua),
        ];
    }

    private function detectBrowser(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'Chrome/') && ! str_contains($ua, 'Edg/') => 'Chrome',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Safari/') && ! str_contains($ua, 'Chrome/') => 'Safari',
            str_contains($ua, 'MSIE') || str_contains($ua, 'Trident/') => 'Internet Explorer',
            default => 'Unknown',
        };
    }

    private function detectDevice(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Android') && str_contains($ua, 'Mobile') => 'Android Phone',
            str_contains($ua, 'Android') => 'Android Tablet',
            str_contains($ua, 'Macintosh') => 'Mac',
            str_contains($ua, 'Windows') => 'Windows PC',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Unknown',
        };
    }

    private function detectOs(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Windows NT 10') => 'Windows 10/11',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS X') => 'macOS',
            str_contains($ua, 'iPhone OS') || str_contains($ua, 'CPU iPhone OS') => 'iOS',
            str_contains($ua, 'iPad') => 'iPadOS',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Unknown',
        };
    }
}
