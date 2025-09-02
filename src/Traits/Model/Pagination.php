<?php

namespace Lumio\Traits\Model;

use Lumio\Config;

trait Pagination {

    /**
     * Switch for pagination
     *
     * @var bool
     */
    private bool $_is_pagination_enabled = true;

    /**
     * Count of records per page
     *
     * date 28.4.2025
     *
     * @var int
     */
    protected int $_records_per_page = 50;

    /**
     * Current page number
     *
     * date 28.4.2025
     *
     * @var int|string
     */
    protected int|string $_current_page = 1;

    /**
     * Total records count
     *
     * date 28.4.2025
     *
     * @var int
     */
    protected int $_total_records = 0;

    /**
     * Special value for page to get total records count
     *
     * date 28.4.2025
     *
     * @var string
     */
    public const PAGE_GET_TOTAL = '--GET-TOTAL--';

    /**
     * Enable or disable pagination
     *
     * @param bool $is_enabled
     *
     * @return self
     */
    public function paginate(bool $is_enabled = true): self {

        $this->_is_pagination_enabled = $is_enabled;

        return $this;
    }

    /**
     * Set the current page
     *
     * @param int|string $page
     *
     * @return self
     */
    public function page(int|string $page): self {

        $this->_current_page = $page;

        return $this;
    }

    /**
     * Set the number of records per page
     *
     * @param int $records
     *
     * @return self
     */
    public function per_page(int $records): self {

        $this->_records_per_page = $records;

        return $this;
    }

    /**
     * Initialize pagination
     *
     * @return void
     *
     * @throws \Exception
     */
    public function _init_pagination() {

        $per_page = Config::get('app.pagination.per_page');
        if (!empty($per_page)) {
            $this->_records_per_page = $per_page;
        }
    }

    /**
     * Returns if pagination is enabled
     *
     * @return bool
     */
    public function is_paginate() : bool {
        return $this->_is_pagination_enabled;
    }

    /**
     * Get total records
     *
     * @return int
     */
    public function get_total(): int {
        return $this->_total_records;
    }

    /**
     * Get current page
     *
     * date 28.4.2025
     *
     * @return int|string
     */
    public function get_page(): int|string {
        return $this->_current_page;
    }

    /**
     * Get records per page
     *
     * @return int
     */
    public function get_per_page(): int {
        return $this->_records_per_page;
    }

    /**
     * Generate LIMIT and OFFSET for current pagination settings
     *
     * @return array
     */
    protected function get_limit(): array {

        if ($this->_current_page < 1) {
            return [null, null];
        }

        $offset = ($this->_current_page - 1) * $this->_records_per_page;

        return [$this->_records_per_page, $offset];
    }

    /**
     * Is the total requested?
     *
     * @return bool
     */
    public function is_pagination_get_total(): bool {
        return $this->_is_pagination_enabled && $this->_current_page === self::PAGE_GET_TOTAL;
    }

}
