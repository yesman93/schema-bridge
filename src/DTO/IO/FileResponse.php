<?php

namespace Lumio\DTO\IO;

use Lumio\Traits;

class FileResponse {

    use Traits\IO\HttpStatus;

    /**
     * File path
     *
     * @var string
     */
    private string $_file_path;

    /**
     * File name
     *
     * @var string
     */
    private string $_file_name;

    /**
     * Response for file download
     *
     * @param string $file_path
     * @param string|null $file_name
     *
     * @return void
     */
    public function __construct(string $file_path, ?string $file_name = null) {
        $this->_file_path = $file_path;
        $this->_file_name = $file_name ?? basename($file_path);
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function get_file_path(): string {
        return $this->_file_path;
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function get_file_name(): string {
        return $this->_file_name;
    }

    /**
     * Get file mime type
     *
     * @return string
     */
    public function get_mime_type(): string {
        return mime_content_type($this->_file_path);
    }

    /**
     * Get file size
     *
     * @return int
     */
    public function get_size(): int {
        return filesize($this->_file_path);
    }

}
