<?php

namespace SchemaBridge;

use RuntimeException;

final class Bootstrap
{

    /**
     * Load given config file from config directory
     *
     * @param string $file Config file name without .php extension
     *
     * @return array
     *
     * @throws RuntimeException If config file not found
     */
    public static function config(string $file): array
    {

        $path = __DIR__ . '/../config/' . $file . '.php';
        if (!is_file($path)) {
            throw new RuntimeException("Config not found: $file");
        }

        $cfg = require $path;

        return $cfg;
    }

    /**
     * Ensure that required directories exist
     *
     * @return void
     */
    public static function ensure_dirs(): void
    {

        $app = self::config('app');
        foreach (['upload_dir','log_dir'] as $k) {
            $dir = $app[$k] ?? null;
            if ($dir && !is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
        }
    }

}
