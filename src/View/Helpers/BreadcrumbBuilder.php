<?php

namespace Lumio\View\Helpers;

use Lumio\DTO\View\Breadcrumb as BreadcrumbDTO;
use Lumio\DTO\View\BreadcrumbItem;

class BreadcrumbBuilder {

    /**
     * Breadcrumb DTO
     *
     * @var BreadcrumbDTO
     */
    protected BreadcrumbDTO $_breadcrumb;

    /**
     * Breadcrumb builder
     *
     * @return void
     */
    public function __construct() {
        $this->_breadcrumb = new BreadcrumbDTO();
    }

    /**
     * Add breadcrumb item
     *
     * @return BreadcrumbBuilder
     */
    public function clear(): self {
        $this->_breadcrumb->clear();
        return $this;
    }

    /**
     * Add breadcrumb item
     *
     * @param BreadcrumbItem $item
     *
     * @return BreadcrumbBuilder
     */
    public function add(BreadcrumbItem $item): self {
        $this->_breadcrumb->add($item);
        return $this;
    }

    /**
     * Get breadcrumb DTO containing all items
     *
     * @return BreadcrumbDTO
     */
    public function get(): BreadcrumbDTO {
        return $this->_breadcrumb;
    }

    /**
     * Check if breadcrumb ends with given item
     *
     * @param BreadcrumbItem $item
     *
     * @return bool
     */
    public function ends_with(BreadcrumbItem $item): bool {
        return $this->_breadcrumb->ends_with($item);
    }

}
