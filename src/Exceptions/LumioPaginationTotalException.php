<?php

namespace Lumio\Exceptions;

class LumioPaginationTotalException extends \Exception {

    /**
     * Total number of records
     *
     * @author TB
     * date 28.4.2025
     *
     * @var int
     */
    protected int $_total;

    /**
     * Number of records per page
     *
     * @author TB
     * date 28.4.2025
     *
     * @var int
     */
    protected int $_per_page;

    /**
     * Exception for getting total number of records
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param int $total
     * @param int $per_page
     *
     * @return LumioPaginationTotalException
     */
    public function __construct(int $total, int $per_page) {

        parent::__construct('Pagination total requested');

        $this->_total = $total;
        $this->_per_page = $per_page;
    }

    /**
     * Get total number of records
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return int
     */
    public function get_total(): int {
        return $this->_total;
    }

    /**
     * Get number of records per page
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return int
     */
    public function get_per_page(): int {
        return $this->_per_page;
    }

}

