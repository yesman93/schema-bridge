<?php

namespace SchemaBridge\Csv;

class Sniffer
{

    /**
     * Try to detect the most likely delimiter from the first line
     *
     * @param string $path Path to CSV file
     *
     * @return string Detected delimiter, default is comma
     */
    public static function detect_delimiter(string $path): string
    {

        $candidates = [",", ";", "\t", "|"];
        $line = fgets(fopen($path, 'r')) ?: '';

        $best = ',';
        $best_count = -1;

        foreach ($candidates as $d) {

            $count = substr_count($line, $d);
            if ($count > $best_count) {
                $best_count = $count;
                $best = $d;
            }
        }

        return $best;
    }

}
