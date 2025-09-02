<?php

namespace Lumio\View\Components\Listview;

class Action {

    /**
     * Link to the action
     *
     *
     * @var string
     */
    private string $_link;

    /**
     * Key to retrieve parameter from the row
     *
     *
     * @var string
     */
    private string $_key;

    /**
     * Label of the action
     *
     *
     * @var string
     */
    private string $_label;

    /**
     * Icon of the action
     *
     *
     * @var string|null
     */
    private ?string $_icon = null;

    /**
     * Whether the action should be opened in a modal
     *
     *
     * @var bool
     */
    private bool $_is_modal = false;

    /**
     * Size of the modal
     *
     *
     * @var string|null
     */
    private ?string $_modal_size = null;

    /**
     * Wheteher to insert divider before the action
     *
     *
     * @var bool
     */
    private bool $_divider_before = false;

    /**
     * Listview action
     *
     *
     * @param string        $link           Link to the action
     * @param string        $key            Key to retrieve parameter from the row and append to the link
     * @param string        $label          Label of the action
     * @param string|null   $icon           Icon of the action (optional)
     * @param bool          $is_modal       Whether the action should be opened in a modal (optional)
     * @param string|null   $modal_size     Size of the modal (optional)
     * @param bool          $divider_before Whether to insert divider before the action (optional)
     *
     * @return void
     */
    public function __construct(
        string $link,
        string $key,
        string $label,
        ?string $icon = null,
        bool $is_modal = false,
        ?string $modal_size = '',
        bool $divider_before = false
    ) {

        $this->_link = $link;
        $this->_key = $key;
        $this->_label = $label;
        $this->_icon = $icon;
        $this->_is_modal = $is_modal;
        $this->_modal_size = $modal_size;
        $this->_divider_before = $divider_before;
    }

    /**
     * Get the link
     *
     *
     * @return string
     */
    public function get_link(): string {
        return $this->_link;
    }

    /**
     * Get the key
     *
     *
     * @return string
     */
    public function get_key(): string {
        return $this->_key;
    }

    /**
     * Get the label
     *
     *
     * @return string
     */
    public function get_label(): string {
        return $this->_label;
    }

    /**
     * Get the icon
     *
     *
     * @return string|null
     */
    public function get_icon(): ?string {
        return $this->_icon;
    }

    /**
     * Get if action should be opened in a modal
     *
     *
     * @return bool
     */
    public function is_modal(): bool {
        return $this->_is_modal;
    }

    /**
     * Get the size of the modal
     *
     *
     * @return string|null
     */
    public function get_modal_size(): ?string {
        return $this->_modal_size;
    }

    /**
     * Get if divider should be inserted before the action
     *
     *
     * @return bool
     */
    public function is_divider_before(): bool {
        return $this->_divider_before;
    }

    /**
     * Get the full link with parameter from the given row
     *
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get_full_link(mixed $row): string {

        $param = $row[$this->_key] ?? null;

        if (empty($param)) {
            return $this->_link;
        }

        $param = urlencode((string)$param);

        $base_link = rtrim($this->_link, '/') . '/';

        return $base_link . $param;
    }


}

