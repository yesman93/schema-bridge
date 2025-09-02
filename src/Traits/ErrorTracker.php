<?php

namespace Lumio\Traits;

trait ErrorTracker {

    /**
     * Errors
     *
     * @author TB
     * @date 1.6.2025
     *
     * @var array
     */
    private array $_errors = [];

    /**
     * Adds an error
     *
     * @author TB
     * @date 1.6.2025
     *
     * @param string $error
     *
     * @return void
     */
    public function add_error(string $error): void {
        $this->_errors[] = $error;
    }

    /**
     * Clears all errors
     *
     * @author TB
     * @date 1.6.2025
     *
     * @return void
     */
    public function clear_errors(): void {
        $this->_errors = [];
    }

    /**
     * Gets all errors
     *
     * @author TB
     * @date 1.6.2025
     *
     * @param bool $clear
     *
     * @return array
     */
    public function get_errors(bool $clear = false): array {

        $errors = $this->_errors;

        if ($clear) {
            $this->clear_errors();
        }

        return $errors;
    }

    /**
     * Check if there are any errors
     *
     * @author TB
     * @date 1.6.2025
     *
     * @return bool
     */
    public function is_errors(): bool {
        return $this->_errors !== [];
    }

}
