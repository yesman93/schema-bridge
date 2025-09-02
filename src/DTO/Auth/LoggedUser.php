<?php

namespace Lumio\DTO\Auth;

use Lumio\Config;

class LoggedUser {

    /**
     * Required fields that must be present in the data
     *
     *
     * @var array
     */
    private array $_required_fields = [];

    /**
     * Stored data
     *
     *
     * @var array
     */
    private array $_data = [];

    /**
     * DTO for logged user - flexible, required core properties + dynamic other data
     *
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data) {

        $required = Config::get('auth/logged_required');
        $this->_required_fields = !empty($required) ? $required : [];

        foreach ($this->_required_fields as $field) {

            if (!array_key_exists($field, $data)) {
                throw new \Exception("Missing required field '$field' in LoggedUser DTO initialization");
            }
        }

        $this->_data = $data;
    }

    /**
     * Dynamic getter
     *
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key) : mixed {
        return $this->_data[$key] ?? null;
    }

    /**
     * Check if property exists
     *
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Get all stored data
     *
     *
     * @return array
     */
    public function data(): array {
        return $this->_data;
    }

}
