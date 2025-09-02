<?php

namespace Lumio\DTO\IO;

use Lumio\Traits;

class JsonResponse {

    use Traits\IO\HttpStatus;

    /**
     * Data to be rendered as JSON
     *
     * @var array
     */
    private array $_data = [];

    /**
     * Data as a JSON string
     *
     * @var string
     */
    private string $_data_json = '';

    /**
     * Length of the JSON string (size in bytes)
     *
     * @var int
     */
    private int $_length = 0;

    /**
     * Response for JSON rendering
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data) {

        $this->_data = $data;

        $this->_to_json();
    }

    /**
     * Creates JSON string from data
     *
     * @return void
     */
    private function _to_json(): void {

        $this->_data_json = json_encode($this->_data, JSON_PRETTY_PRINT);
        $this->_length = strlen($this->_data_json);
    }

    /**
     * Get JSON data
     *
     * @return array
     */
    public function get_data(): array {
        return $this->_data;
    }

    /**
     * Render JSON data as a string
     *
     * @return string
     */
    public function __toString(): string {
        return $this->_data_json;
    }

    /**
     * Get length of JSON data
     *
     * @return int
     */
    public function get_length(): int {
        return $this->_length;
    }

}


