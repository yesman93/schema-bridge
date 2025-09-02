<?php

namespace Lumio\DTO\View;

class FormReadableValue {

    /**
     * Name of the input
     *
     *
     * @var string
     */
    private string $_name = '';

    /**
     * Readable value of the input
     *
     *
     * @var mixed
     */
    private mixed $_value;

    /**
     * Label of the input
     *
     *
     * @var string
     */
    private string $_label = '';

    /**
     * Stored readable selected/filled value of the form input
     *
     *
     * @param string $name
     * @param mixed $value
     * @param string $label
     *
     * @return void
     */
    public function __construct(string $name, mixed $value, string $label) {

        $this->_name = $name;
        $this->_value = $value;
        $this->_label = $label;
    }

    /**
     * Get the name of the input
     *
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

    /**
     * Get the value of the input
     *
     *
     * @return mixed
     */
    public function get_value(): mixed {
        return $this->_value;
    }

    /**
     * Get the label of the input
     *
     *
     * @return string
     */
    public function get_label(): string {
        return $this->_label;
    }

}
