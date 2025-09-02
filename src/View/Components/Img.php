<?php

namespace Lumio\View\Components;

use Lumio\Config;
use Lumio\View\Component;

class Img extends Component {

    /**
     * HTML code of the image
     *
     * @var string
     */
    private string $_html = '';

    /**
     * Build image tag
     *
     * @param string $src
     * @param array|null $attributes
     *
     * @return self
     */
    public static function build(string $src, ?array $attributes = null) : self {

        $instance = new self();

        $attributes = $attributes ?? [];

        try {

            $dir = Config::get('app.view.path_assets_public');
            $dir .= DIRECTORY_SEPARATOR . 'images';

            $uri_private = Config::get('app.storage.private.uri_show_file');

        } catch (\Throwable $e) {
            $dir = '';
            $uri_private = '';
        }

        if (!empty($uri_private) && strpos($src, $uri_private) !== false) {
            $attributes['src'] = $src;
        } else {
            $attributes['src'] = $dir . DIRECTORY_SEPARATOR . $src;
        }

        $instance->_html = '<img ' . $instance->_get_attr($attributes) . ' />';

        return $instance;
    }

    /**
     * Get HTML code of the image
     *
     * @return string
     */
    public function get() : string {
        return $this->_html;
    }

    /**
     * Render HTML code of the image
     *
     * @return void
     */
    public function render() : void {
        echo $this->get();
    }

}
