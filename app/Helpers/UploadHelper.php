<?php

namespace App\Helpers;

class UploadHelper
{
    /**
     * Récupérer la limite upload_max_filesize de PHP (en bytes)
     */
    public static function getPhpUploadMaxSize(): int
    {
        return self::parseSize(ini_get('upload_max_filesize'));
    }

    /**
     * Récupérer en kilo-octets
     */
    public static function getPhpUploadMaxSizeKB(): int
    {
        return (int) round(self::getPhpUploadMaxSize() / 1024);
    }

    /**
     * Récupérer en méga-octets
     */
    public static function getPhpUploadMaxSizeMB(): float
    {
        return round(self::getPhpUploadMaxSize() / (1024 * 1024), 2);
    }

    /**
     * Récupérer la limite post_max_size de PHP (en bytes)
     */
    public static function getPostMaxSize(): int
    {
        return self::parseSize(ini_get('post_max_size'));
    }

    /**
     * Récupérer post_max_size en kilo-octets
     */
    public static function getPostMaxSizeKB(): int
    {
        return (int) round(self::getPostMaxSize() / 1024);
    }

    /**
     * Récupérer post_max_size en méga-octets
     */
    public static function getPostMaxSizeMB(): float
    {
        return round(self::getPostMaxSize() / (1024 * 1024), 2);
    }

    /**
     * Parser taille PHP ("10M", "512K", "2G") en bytes
     */
    private static function parseSize(string $size): int
    {
        $unit = strtolower(substr($size, -1));
        $value = (int) substr($size, 0, -1);

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $size,
        };
    }
}
