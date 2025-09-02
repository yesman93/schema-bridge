<?php

namespace Lumio\DTO\View;

class Breadcrumb {

    /**
     * Breadcrumb items
     *
     * @author TB
     * @date 19.5.2025
     *
     * @var BreadcrumbItem[]
     */
    protected array $_items = [];

    /**
     * Add breadcrumb item
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param BreadcrumbItem $item
     *
     * @return void
     */
    public function add(BreadcrumbItem $item): void {
        $this->_items[] = $item;
    }

    /**
     * Clear all breadcrumb items
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return void
     */
    public function clear(): void {
        $this->_items = [];
    }

    /**
     * Get all breadcrumb items
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return array
     */
    public function get(): array {
        return $this->_items;
    }

    /**
     * Get the last breadcrumb item
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return BreadcrumbItem|null
     */
    public function last(): ?BreadcrumbItem {
        return empty($this->_items) ? null : end($this->_items);
    }

    /**
     * Get count of breadcrumb items
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return int
     */
    public function count(): int {
        return count($this->_items);
    }

    /**
     * Check if breadcrumb ends with given item
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param BreadcrumbItem $item
     *
     * @return bool
     */
    public function ends_with(BreadcrumbItem $item): bool {

        if (($last = $this->last()) === null) {
            return false;
        }

        return $last->is($item);
    }

}
