<?php

namespace Lumio\DTO\View;

class BreadcrumbItem {

    /**
     * URL of the breadcrumb item
     *
     * @var string
     */
    private string $_url = '';

    /**
     * Label of the breadcrumb item
     *
     * @var string|null
     */
    private ?string $_label = null;

    /**
     * Breadcrumb item
     *
     * @param string $url
     * @param string|null $label
     *
     * @return void
     */
    public function __construct(string $url, ?string $label = null) {

        $this->_url = $url;
        $this->_label = $label;
    }

    /**
     * Get URL of the breadcrumb item
     *
     * @return string
     */
    public function get_url(): string {
        return $this->_url;
    }

    /**
     * Get label of the breadcrumb item
     *
     * @return string
     */
    public function get_label(): string {
        return $this->_label === null || $this->_label === '' ? $this->_url : $this->_label;
    }

    /**
     * Check if given breadcrumb item is the same as this one
     *
     * @param BreadcrumbItem $item
     *
     * @return bool
     */
    public function is(BreadcrumbItem $item): bool {
        return $this->_url === $item->get_url() && $this->_label === $item->get_label();
    }

}
