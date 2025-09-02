<?php

namespace Lumio\Traits\Model;

trait Filter {

    use Search;

    /**
     * Filter data
     *
     * @var array|null
     */
    private ?array $_filter_data = null;

    /**
     * Set given filter. If its a search query, set it to the search query instead of filter data
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function filter(string $name, mixed $value): void {

        if ($name === 'search_query') {
            $this->set_search_query($value);
        } else {
            $this->_filter_data[$name] = $value;
        }
    }

    /**
     * Set given filter data. If ti contains a search query, set it to the search query instead of filter data
     *
     * @param array $data
     *
     * @return void
     */
    public function set_filter_data(array $data): void {

        if (empty($data)) {
            return;
        }

        foreach ($data as $key => $value) {

            if ($key === 'search_query') {
                $this->set_search_query($value);
            } else {
                $this->_filter_data[$key] = $value;
            }
        }
    }

    /**
     * Replace current filter data with given data. If it contains a search query, set it to the search query instead of filter data
     *
     * @param array $data
     *
     * @return void
     */
    public function replace_filter_data(array $data): void {

        if (isset($data['search_query'])) {
            $this->set_search_query($data['search_query']);
            unset($data['search_query']);
        }

        $this->_filter_data = $data;
    }

}

