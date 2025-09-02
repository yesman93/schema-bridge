<?php

namespace Lumio\DTO\IO;

use Lumio\Traits;

class JsonResponse {

    use Traits\IO\HttpStatus;

    /**
     * Data to be rendered as JSON
     *
     * @author TB
     * @date 14.5.2025
     *
     * @var array
     */
    private array $_data = [];

    /**
     * Data as a JSON string
     *
     * @author TB
     * @date 14.5.2025
     *
     * @var string
     */
    private string $_data_json = '';

    /**
     * Length of the JSON string (size in bytes)
     *
     * @author TB
     * @date 14.5.2025
     *
     * @var int
     */
    private int $_length = 0;

    /**
     * Response for JSON rendering
     *
     * @author TB
     * @date 14.5.2025
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
     * @author TB
     * @date 14.5.2025
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
     * @author TB
     * @date 14.5.2025
     *
     * @return array
     */
    public function get_data(): array {
        return $this->_data;
    }

    /**
     * Render JSON data as a string
     *
     * @author TB
     * @date 14.5.2025
     *
     * @return string
     */
    public function __toString(): string {
        return $this->_data_json;
    }

    /**
     * Get length of JSON data
     *
     * @author TB
     * @date 14.5.2025
     *
     * @return int
     */
    public function get_length(): int {
        return $this->_length;
    }

}


