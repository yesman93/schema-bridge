<?php

namespace Lumio\View\Components;

use Lumio\View\Component;

class Breadcrumb extends Component {

    /**
     * Breadcrumb HTML
     *
     * @author TB
     * @date 19.5.2025
     *
     * @var string
     */
    private string $_html = '';

    /**
     * Build breadcrumb instance
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return Breadcrumb
     */
    public static function build(): Breadcrumb {

        $instance = new self();

        if (self::$_breadcrumb === null || self::$_breadcrumb->count() === 0) {
            return $instance;
        }

        $instance->_html = '
            <ul class="breadcrumb">';

        $items = self::$_breadcrumb->get();
        foreach ($items as $item) {

            $url = $item->get_url();
            $host = parse_url($url, PHP_URL_HOST);
            if (empty($host)) {
                $url = '/' . ltrim($url, '/');
            }

            $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars($item->get_label(), ENT_QUOTES, 'UTF-8');

            $instance->_html .= '
                <li class="breadcrumb-item"><a href="' . $url . '">' . $label . '</a></li>';
        }

        $instance->_html .= '
            </ul>';

        return $instance;
    }

    /**
     * Get the breadcrumb HTML
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return string
     */
    public function get(): string {
        return $this->_html;
    }

    /**
     * Render the breadcrumb HTML
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return void
     */
    public function render(): void {
        echo $this->get();
    }

}
