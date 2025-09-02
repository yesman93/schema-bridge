<?php

namespace Lumio\DTO\Model;

class Sorting {

    /**
     * Sorting direction - ascending
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string
     */
    public const ASC  = 'asc';

    /**
     * Sorting direction - descending
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string
     */
    public const DESC = 'desc';

    /**
     * Column by which to sort
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string
     */
    private string $_column;

    /**
     * Direction in which to sort (asc|desc)
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string
     */
    private string $_direction;

    /**
     * Sorting
     *
     * @author TB
     * @date 6.5.2025
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
     * @author TB
     * @date 6.5.2025
     *
     * @return string
     */
    public function get_column(): string {
        return $this->_column;
    }

    /**
     * Get the direction in which to sort
     *
     * @author TB
     * date 6.5.2025
     *
     * @return string
     */
    public function get_direction(): string {
        return $this->_direction;
    }

}
