<?php
declare(strict_types=1);

namespace LifeXP\Core;

final class Cache
{
    /** Returns the cached array for this key if it is fresh, otherwise builds it and stores it. */
    public static function remember(string $key, callable $build, int $ttl = 60): array
    {
        $dir  = dirname(__DIR__, 2) . '/cache';
        $file = $dir . '/' . md5($key) . '.json';

        if (is_file($file) && filemtime($file) >= time() - $ttl) {
            $json = @file_get_contents($file);
            $data = $json === false ? null : json_decode($json, true);
            if (is_array($data)) {
                return $data;
            }
        }

        $data = $build();

        if (is_dir($dir) || @mkdir($dir, 0755)) {
            @file_put_contents($file, json_encode($data), LOCK_EX);
        }

        return $data;
    }
}