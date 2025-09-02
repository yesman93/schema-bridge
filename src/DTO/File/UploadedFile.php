<?php

namespace Lumio\DTO\File;

class UploadedFile {

    /**
     * Temporary file path
     *
     * @author TB
     * @date 27.5.2025
     *
     * @var string
     */
    private string $_tmp_path;

    /**
     * Original file name
     *
     * @author TB
     * @date 27.5.2025
     *
     * @var string
     */
    private string $_name;

    /**
     * File size in bytes
     *
     * @author TB
     * @date 27.5.2025
     *
     * @var int
     */
    private int $_size;

    /**
     * MIME type of the file
     *
     * @author TB
     * @date 27.5.2025
     *
     * @var string
     */
    private string $_type;

    /**
     * Upload error code
     *
     * @author TB
     * @date 27.5.2025
     *
     * @var int
     */
    private int $_error;

    /**
     * Uploaded file
     *
     * @author TB
     * @date 27.5.2025
     *
     * @param string $tmp_path
     * @param string $name
     * @param int $size
     * @param string $type
     * @param int $error
     *
     * @return void
     */
    public function __construct(string $tmp_path, string $name, int $size, string $type, int $error) {

        $this->_tmp_path = $tmp_path;
        $this->_name = $name;
        $this->_size = $size;
        $this->_type = $type;
        $this->_error = $error;
    }

    /**
     * Get temporary file path
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return string
     */
    public function get_tmp_path(): string {
        return $this->_tmp_path;
    }

    /**
     * Get original file name
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

    /**
     * Get file size in bytes
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return int
     */
    public function get_size(): int {
        return $this->_size;
    }

    /**
     * Get MIME type of the file
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return string
     */
    public function get_type(): string {
        return $this->_type;
    }

    /**
     * Get upload error code
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return int
     */
    public function get_error(): int {
        return $this->_error;
    }

    /**
     * Get file extension
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return string
     */
    public function get_extension(): string {
        return strtolower(pathinfo($this->_name, PATHINFO_EXTENSION));
    }

    /**
     * Check if the uploaded file is valid - no errors and is an uploaded file
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return bool
     */
    public function is_valid(): bool {
        return $this->_error === UPLOAD_ERR_OK && is_uploaded_file($this->_tmp_path);
    }

    /**
     * Check if the uploaded file is an image
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return bool
     */
    public function is_image(): bool {
        return in_array($this->get_extension(), ['jpg', 'jpeg', 'png', 'gif', 'bmp'], true);
    }

    /**
     * Check if the uploaded file is a document
     *
     * @author TB
     * @date 27.5.2025
     *
     * @return bool
     */
    public function is_document(): bool {
        return in_array($this->get_extension(), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'rtf', 'odt', 'ods'], true);
    }

    /**
     * Save the uploaded file to given directory
     *
     * @author TB
     * @date 27.5.2025
     *
     * @param string $dir Directory where the file should be saved. If it does not exist, it will be created
     * @param string|null $file_name Optional custom file name. If not provided original name will be used
     * @param int $dir_permissions Permissions for the directory (default is 0777) if the directory needs to be created
     *
     * @return bool
     */
    public function save(string $dir, ?string $file_name = null, int $dir_permissions = 0777): bool {

        if (!$this->is_valid() || $dir === '') {
            return false;
        }

        if (!is_dir($dir)) {
            mkdir($dir, $dir_permissions, true);
        }

        $final_name = $file_name ?? $this->_name;
        $target = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $final_name;

        return move_uploaded_file($this->_tmp_path, $target);
    }

    /**
     * Create an UploadedFile instance from raw $_FILES data
     *
     * @author TB
     * @date 27.5.2025
     *
     * @param array $raw Raw $_FILES data - entry for one file input filed, e.g. $_FILES['file_input_name']
     *
     * @return null|UploadedFile|array Returns a single UploadedFile instance or an array of UploadedFile instances for multiple files, or null if no file was uploaded
     */
    public static function from_request(array $raw): null|UploadedFile|array {

        if (!isset($raw['tmp_name'])) {
            return null;
        }

        // Multiple files
        if (is_array($raw['tmp_name'])) {

            $files = [];
            foreach ($raw['tmp_name'] as $i => $tmp_path) {

                if (empty($tmp_path)) {
                    continue;
                }

                $files[] = new self(
                    $tmp_path,
                    $raw['name'][$i] ?? '',
                    $raw['size'][$i] ?? 0,
                    $raw['type'][$i] ?? '',
                    $raw['error'][$i] ?? UPLOAD_ERR_NO_FILE
                );
            }
            return $files;
        }

        // Single file
        return new self(
            $raw['tmp_name'],
            $raw['name'],
            $raw['size'],
            $raw['type'],
            $raw['error']
        );
    }

}
