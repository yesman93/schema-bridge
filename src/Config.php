<?php

namespace Lumio;

class Config {

    /**
     * Cache of loaded configs
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var array
     */
    private static array $_cache = [];

    /**
     * Directory where config files are stored
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    private const CONFIG_DIR = __DIR__ . '/../config';

    /**
     * Get a configuration array by name
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $name Config file name without .php (e.g., 'database' or 'services/fio') with support for array keys
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function get(string $name): mixed {

        $name = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $name);

        // Separate file and optional key path
        $parts = explode('.', $name, 2);
        $file_part = $parts[0];           // example: "app" or "services/fio"
        $key_part = $parts[1] ?? null;     // example: "pagination.per_page" or "lvl1.lvl2.lvl3"

        if (!isset(self::$_cache[$file_part])) {

            $fpath = self::CONFIG_DIR . DIRECTORY_SEPARATOR . $file_part . '.php';

            if (!file_exists($fpath)) {
                throw new \Exception("Config file not found: $fpath");
            }

            $config = require $fpath;

            if (!is_array($config)) {
                throw new \Exception("Config file must return an array: $fpath");
            }

            self::$_cache[$file_part] = $config;
        }

        $config = self::$_cache[$file_part];

        // If no key path is provided, return the whole config array
        if (is_null($key_part)) {
            return $config;
        }

        // Traverse the config array using the key path
        $keys = explode('.', $key_part);
        foreach ($keys as $key) {

            if (!array_key_exists($key, $config)) {
                throw new \Exception("Key not found in config: $key");
            }

            $config = $config[$key];
        }

        return $config;
    }

}
