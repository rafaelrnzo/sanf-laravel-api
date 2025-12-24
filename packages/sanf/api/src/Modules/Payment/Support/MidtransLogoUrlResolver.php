<?php

namespace Sanf\Api\Modules\Payment\Support;

use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class MidtransLogoUrlResolver
{
    /**
     * Cache for resolved bank logo URLs to avoid repeated filesystem lookups.
     *
     * @var array<string, string|null>
     */
    private static array $cache = [];

    public static function resolve(?string $bankName): ?string
    {
        if ($bankName === null || $bankName === '') {
            return null;
        }

        $normalized = self::normalize($bankName);

        if (array_key_exists($normalized, self::$cache)) {
            return self::$cache[$normalized];
        }

        $diskName = self::resolveDiskName();
        $disk = Storage::disk($diskName);
        $directory = self::resolveDirectory();

        $path = self::resolveFromConfiguredMap($normalized, $directory);

        if (!$path) {
            return self::$cache[$normalized] = null;
        }

        try {
            return self::$cache[$normalized] = self::generateUrl($disk, $path, $diskName);
        } catch (Throwable $exception) {
            return self::$cache[$normalized] = null;
        }
    }

    private static function generateUrl(FilesystemAdapter $disk, string $path, string $diskName): ?string
    {
        if (self::shouldUseTemporaryUrl($diskName) && method_exists($disk, 'temporaryUrl')) {
            return $disk->temporaryUrl($path, Carbon::now()->addDay());
        }

        return $disk->url($path);
    }

    private static function resolveDiskName(): string
    {
        return config('midtrans.logo.disk')
            ?? config('filesystems.default', 'local');
    }

    private static function resolveDirectory(): string
    {
        return trim(config('midtrans.logo.directory'), '/');
    }

    private static function resolveFromConfiguredMap(string $normalized, string $directory): ?string
    {
        $map = config('midtrans.logo.map', []);

        if (!isset($map[$normalized])) {
            return null;
        }

        return self::formatPath($map[$normalized], $directory);
    }

    private static function formatPath(string $path, string $directory): string
    {
        if (strpos($path, '/') !== false) {
            return ltrim($path, '/');
        }

        if ($directory === '') {
            return ltrim($path, '/');
        }

        return $directory . '/' . ltrim($path, '/');
    }

    private static function shouldUseTemporaryUrl(string $diskName): bool
    {
        return self::getDiskDriver($diskName) === 's3';
    }

    private static function getDiskDriver(string $diskName): ?string
    {
        return config(sprintf('filesystems.disks.%s.driver', $diskName));
    }

    private static function normalize(string $value): string
    {
        $value = strtolower($value);

        return preg_replace('/[^a-z0-9]/', '', $value) ?? '';
    }
}
