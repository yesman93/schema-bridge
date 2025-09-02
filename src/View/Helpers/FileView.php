<?php

namespace Lumio\View\Helpers;

class FileView {

    /**
     * Get Font Awesome icon based on file extension of the given filename
     *
     *
     * @param string $filename
     *
     * @return string
     */
    public static function get_icon(string $filename): string {

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp' => 'fa-file-image',
            'zip', 'rar', 'gz', 'tar', 'iso' => 'fa-file-archive',
            'mp3', 'vma', 'wav', 'aac', 'flac' => 'fa-file-audio',
            'mp4', 'mov', 'wmv', 'flv', 'avi', 'mkv' => 'fa-file-video',
            'pdf' => 'fa-file-pdf',
            'doc', 'docx' => 'fa-file-word',
            'xls', 'xlsx' => 'fa-file-excel',
            'ppt', 'pptx' => 'fa-file-powerpoint',
            'txt', 'md' => 'fa-file-lines',
            'csv' => 'fa-file-csv',
            default => 'fa-file'
        };
    }

    /**
     * Check if the file is previewable in browser based on its extension
     *
     *
     * @param string $filename
     *
     * @return bool
     */
    public static function is_previewable(string $filename): bool {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp', 'pdf', 'txt', 'md'], true);
    }

    /**
     * Convert bytes to a human readable format
     *
     *
     * @param int $bytes
     *
     * @return string
     */
    public static function readable_size(int $bytes): string {

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            ++$i;
        }

        if ($i === 0) {
            return (int)$bytes . ' ' . $units[$i];
        }

        $formatted = number_format($bytes, 2, ',', ' ');

        return rtrim(rtrim($formatted, '0'), '.') . ' ' . $units[$i];
    }

}


