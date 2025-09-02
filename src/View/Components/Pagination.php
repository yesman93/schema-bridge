<?php

namespace Lumio\View\Components;

class Pagination {

    private string $_html = '';

    private string $_base_uri = '';

    private string $_filters = '';

    private string $_sorting = '';

    private array $_params = [];

    private string $_counts_info = '';

    /**
     * Build a pagination
     *
     * @param int       $page
     * @param int       $per_page
     * @param int       $total
     * @param string    $base_uri       Base URI before page number (e.g. 'user/users/')
     * @param string    $filters        URL filters (e.g. 'role_id:2')
     * @param string    $sorting        URL sorting (e.g. 'created_at-desc')
     * @param bool      $counts_info    Whether to display counts info
     *
     * @return self
     *@author TB     *
     */
    public static function build(
        int $page,
        int $per_page,
        int $total,
        string $base_uri,
        string $filters = '',
        string $sorting = '',
        array $params = [],
        bool $counts_info = true
    ) : self {

        $instance = new self();

        $total_pages = $per_page > 0 ? (int)ceil($total / $per_page) : 1;

        if ($total_pages < 1) {
            return $instance;
        }

        $instance->_base_uri = '/' . ltrim($base_uri, '/');
        $instance->_filters = $filters;
        $instance->_sorting = $sorting;
        $instance->_params = $params;

        $html = '';

        // Count info
        if ($counts_info) {

            $from = $page <= 1 ? 1 : (($page - 1) * $per_page) + 1;

            $to = ($page >= $total_pages || $total <= $per_page)
                ? $total
                : $page * $per_page;

            $instance->_counts_info = __tx('Shown %s - %s out of %s', $from, $to, $total);

            $html .= '<div class="text-muted fs-12 opacity-50">' . $instance->_counts_info . '</div>';
        }

        $html .= '
            <ul class="pagination mb-0">';

        // First
        if ($page > 2) {
            $html .= '
                ' . self::_li($instance->_link(1), '<i class="fa fa-angle-double-left"></i>', 'První');
        }

        // Previous
        if ($page > 1) {
            $html .= '
                ' . self::_li($instance->_link($page - 1), '<i class="fa fa-angle-left"></i>', 'Předchozí');
        }

        // Main pages with dots
        $was_pre_dots = false;
        $was_after_dots = false;
        for ($i = 1; $i <= $total_pages; $i++) {

            $is_far_prev = ($i < ($page - 2));
            $is_far_next = ($i > ($page + 2));
            $is_far = $is_far_prev || $is_far_next;
            $is_first = $i === 1;
            $is_last = $i === $total_pages;

            if (!$is_far || $is_first || $is_last) {

                $active = $i === $page ? ' active' : '';
                $html .= '
                <li class="page-item' . $active . '"><a class="page-link" tabindex="-1" href="' . $instance->_link($i) . '">' . $i . '</a></li>';

            } elseif (!$is_first && !$is_last && (($is_far_prev && !$was_pre_dots) || ($is_far_next && !$was_after_dots))) {

                $html .= '
                <li class="page-item"><a class="page-link">...</a></li>';

                if ($is_far_prev) {
                    $was_pre_dots = true;
                }
                if ($is_far_next) {
                    $was_after_dots = true;
                }
            }
        }

        // Next
        if ($page < $total_pages) {
            $html .= '
                ' . self::_li($instance->_link($page + 1), '<i class="fa fa-angle-right"></i>', 'Další');
        }

        // Last
        if ($page < ($total_pages - 1)) {
            $html .= '
                ' . self::_li($instance->_link($total_pages), '<i class="fa fa-angle-double-right"></i>', 'Poslední');
        }

        $html .= '
            </ul>';

        $instance->_html = $html;

        return $instance;
    }

    public function get(): string {
        return $this->_html;
    }

    public function render(): void {
        echo $this->_html;
    }

    public function get_counts_info(): string {
        return $this->_counts_info;
    }

    /**
     * Generate link to given page
     *
     * @param int $page
     *
     * @return string
     */
    private function _link(int $page) : string {

        $filters = empty($this->_filters) ? '' : '/' . $this->_filters;
        $sorting = empty($this->_sorting) ? '' : '/' . $this->_sorting;
        $params = empty($this->_params) ? '' : '/' . implode('/', $this->_params);

        return rtrim($this->_base_uri, '/') . '/page-' . $page . $filters . $sorting . $params;
    }

    private static function _li(string $href, string $label, string $sr_text): string {
        return '<li class="page-item"><a class="page-link" tabindex="-1" href="' . $href . '">' . $label . '<span class="sr-only">' . $sr_text . '</span></a></li>';
    }

}
