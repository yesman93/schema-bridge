<?php

namespace Lumio\DTO\View;

class FormSetup {

    /**
     * Form action
     *
     *
     * @var string
     */
    private string $_action;

    /**
     * Form enctype
     *
     *
     * @var string|null
     */
    private ?string $_enctype;

    /**
     * Form wrapper class
     *
     *
     * @var string
     */
    private string $_class_wrapper;

    /**
     * Form card class
     *
     *
     * @var string
     */
    private string $_class_card;

    /**
     * Form header class
     *
     *
     * @var string
     */
    private string $_class_header;

    /**
     * Form body class
     *
     *
     * @var string
     */
    private string $_class_body;

    /**
     * Form class
     *
     *
     * @var string
     */
    private string $_class;

    /**
     * Form id
     *
     *
     * @var string
     */
    private string $_id;

    /**
     * Form data
     *
     *
     * @var array
     */
    private array $_data;

    /**
     * Form data id
     *
     *
     * @var string
     */
    private string $_data_id;

    /**
     * Form is card
     *
     *
     * @var bool|null
     */
    private ?bool $is_card;

    /**
     * Form show title
     *
     *
     * @var bool|null
     */
    private ?bool $show_title;

    /**
     * Form setup
     *
     *
     * @param array $data
     * @param string $data_id
     * @param string $action
     * @param string|null $enctype
     * @param string $class_wrapper
     * @param string $class_card
     * @param string $class_header
     * @param string $class_body
     * @param string $class
     * @param string $id
     * @param bool|null $is_card
     * @param bool|null $show_title
     *
     * @return void
     */
    public function __construct(
        array $data = [],
        string $data_id = '',
        string $action = '',
        ?string $enctype = null,
        string $class_wrapper = '',
        string $class_card = '',
        string $class_header = '',
        string $class_body = '',
        string $class = '',
        string $id = '',
        ?bool $is_card = null,
        ?bool $show_title = null
    ) {
        $this->_data = $data;
        $this->_data_id = $data_id;
        $this->_action = $action;
        $this->_enctype = $enctype;
        $this->_class_wrapper = $class_wrapper;
        $this->_class_card = $class_card;
        $this->_class_header = $class_header;
        $this->_class_body = $class_body;
        $this->_class = $class;
        $this->_id = $id;
        $this->is_card = $is_card;
        $this->show_title = $show_title;
    }

    /**
     * Get action
     *
     *
     * @return string
     */
    public function get_action(): string {
        return $this->_action;
    }

    /**
     * Get enctype
     *
     *
     * @return string|null
     */
    public function get_enctype(): ?string {
        return $this->_enctype;
    }

    /**
     * Get wrapper class
     *
     *
     * @return string
     */
    public function get_class_wrapper(): string {
        return $this->_class_wrapper;
    }

    /**
     * Get card class
     *
     *
     * @return string
     */
    public function get_class_card(): string {
        return $this->_class_card;
    }

    /**
     * Get header class
     *
     *
     * @return string
     */
    public function get_class_header(): string {
        return $this->_class_header;
    }

    /**
     * Get body class
     *
     *
     * @return string
     */
    public function get_class_body(): string {
        return $this->_class_body;
    }

    /**
     * Get form class
     *
     *
     * @return string
     */
    public function get_class(): string {
        return $this->_class;
    }

    /**
     * Get form id
     *
     *
     * @return string
     */
    public function get_id(): string {
        return $this->_id;
    }

    /**
     * Get form data
     *
     *
     * @return array
     */
    public function get_data(): array {
        return $this->_data;
    }

    /**
     * Get form data id
     *
     *
     * @return string
     */
    public function get_data_id(): string {
        return $this->_data_id;
    }

    /**
     * Get whether the form is a card
     *
     *
     * @return bool|null
     */
    public function is_card(): ?bool {
        return $this->is_card;
    }

    /**
     * Get whether to show the title
     *
     *
     * @return bool|null
     */
    public function show_title(): ?bool {
        return $this->show_title;
    }

}
