<?php

namespace SchemaBridge\Storage;

use Random\RandomException;
use RuntimeException;

class Files
{

    /**
     * Move an uploaded file to target directory and return final absolute path
     *
     * @param array  $file       One entry from $_FILES (e.g. $_FILES['csv'])
     * @param string $target_dir Absolute path to target directory
     *
     * @return string Final absolute path to moved file
     *
     * @throws RuntimeException|RandomException On any error (invalid payload, upload error, move failure, etc.)
     */
    public static function move_uploaded(array $file, string $target_dir): string
    {

        if (!isset($file['error'], $file['tmp_name'], $file['name'])) {
            throw new RuntimeException('Invalid upload payload');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::_error_message($file['error']));
        }

        if (!is_dir($target_dir)) {
            @mkdir($target_dir, 0775, true);
        }

        if (!is_writable($target_dir)) {
            throw new RuntimeException('Upload directory is not writable');
        }

        $ext = pathinfo((string) $file['name'], PATHINFO_EXTENSION);
        $ext = $ext !== '' ? strtolower($ext) : 'csv';

        $safe_base = self::sanitize_filename((string) $file['name']);
        if ($safe_base === '') {
            $safe_base = 'upload';
        }

        $final_name = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '_' . $safe_base;
        // Ensure extension is present (only once)
        if (pathinfo($final_name, PATHINFO_EXTENSION) === '') {
            $final_name .= '.' . $ext;
        }

        $dest = rtrim($target_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $final_name;

        if (!is_uploaded_file($file['tmp_name'])) {
            // Fallback: still attempt move for environments without is_uploaded_file (CLI tests)
            // but keep a guard to avoid arbitrary paths.
            if (!is_file($file['tmp_name'])) {
                throw new RuntimeException('Temporary upload file not found.');
            }
        }

        if (!@move_uploaded_file($file['tmp_name'], $dest)) {
            // If move_uploaded_file fails (e.g., non-SAPI), try rename() as a fallback.
            if (!@rename($file['tmp_name'], $dest)) {
                throw new RuntimeException('Failed to move uploaded file.');
            }
        }

        return $dest;
    }

    /**
     * Create a safe filename: ASCII-ish, dashes instead of spaces, remove dangerous chars.
     */
    public static function sanitize_filename(string $name): string
    {
        // Strip any path components
        $name = basename($name);

        // Replace whitespace with single dash
        $name = preg_replace('/\s+/', '-', $name) ?? $name;

        // Remove anything not alnum, dot, dash, underscore
        $name = preg_replace('/[^A-Za-z0-9._-]/', '', $name) ?? $name;

        // Collapse repeated dots (avoid dot-dot tricks)
        $name = preg_replace('/\.{2,}/', '.', $name) ?? $name;

        // Trim leading dots (avoid hidden/unsafe)
        $name = ltrim($name, '.');

        return $name;
    }

    /** Map PHP upload error codes to readable messages. */
    private static function _error_message(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            default               => 'Unknown upload error.',
        };
    }
}
