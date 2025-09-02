<?php

namespace Lumio\DTO\Model;

class Pagination {

    /**
     * Current page
     *
     * @var int
     */
    private int $_page;

    /**
     * Records per page
     *
     * @var int
     */
    private int $_per_page;

    /**
     * Total number of records
     *
     * @var int
     */
    private int $_total;

    /**
     * DTO for pagination parameters
     *
     * @param int $page
     * @param int $per_page
     * @param int $total
     *
     * @return void
     */
    public function __construct(int $page, int $per_page, int $total) {
        $this->_page = $page;
        $this->_per_page = $per_page;
        $this->_total = $total;
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function get_page(): int {
        return $this->_page;
    }

    /**
     * Get records per page
     *
     * @return int
     */
    public function get_per_page(): int {
        return $this->_per_page;
    }

    /**
     * Get total records
     *
     * @return int
     */
    public function get_total(): int {
        return $this->_total;
    }

    /**
     * Get total number of pages
     *
     * @return int
     */
    public function get_pages(): int {
        return (int)ceil($this->_total / max($this->_per_page, 1));
    }

}
