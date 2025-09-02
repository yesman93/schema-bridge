<?php

namespace Lumio\DTO\Model;

class Sorting {

    /**
     * Sorting direction - ascending
     *
     * @var string
     */
    public const ASC  = 'asc';

    /**
     * Sorting direction - descending
     *
     * @var string
     */
    public const DESC = 'desc';

    /**
     * Column by which to sort
     *
     * @var string
     */
    private string $_column;

    /**
     * Direction in which to sort (asc|desc)
     *
     * @var string
     */
    private string $_direction;

    /**
     * Sorting
     *
     * @param string $column
     * @param string $direction
     *
     * @return void
     */
    public function __construct(string $column, string $direction) {
        $this->_column = $column;
        $this->_direction = strtolower($direction) === self::DESC ? self::DESC : self::ASC;
    }

    /**
     * Get the column by which to sort
     *
     * @return string
     */
    public function get_column(): string {
        return $this->_column;
    }

    /**
     * Get the direction in which to sort
     *
     * date 6.5.2025
     *
     * @return string
     */
    public function get_direction(): string {
        return $this->_direction;
    }

}
