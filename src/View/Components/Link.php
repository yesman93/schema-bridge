<?php

namespace Lumio\View\Components;

use Lumio\View\Component;

class Link extends Component {

    /**
     * Size of the modal, that the link opens - sm
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    public const string SIZE_SM = 'sm';

    /**
     * Size of the modal, that the link opens - md
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    public const string SIZE_MD = 'md';

    /**
     * Size of the modal, that the link opens - lg
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    public const string SIZE_LG = 'lg';

    /**
     * Size of the modal, that the link opens - xl
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    public const string SIZE_XL = 'xl';

    /**
     * Size of the modal, that the link opens - xxl
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    public const string SIZE_XXL = 'xxl';

    /**
     * HTML code of the link
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string
     */
    private string $_html = '';

    /**
     * Build a link
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $href
     * @param string|null $label
     * @param array|null $attributes
     *
     * @return self
     */
    public static function a(string $href, ?string $label = null, ?array $attributes = null) : self {

        $instance = new self();
        $label = $label ?: $href;

        $attributes = $attributes ?? [];
        $attributes['href'] = $href;

        $attr = $instance->_get_attr($attributes);

        $instance->_html = '<a ' . $attr . '>' . $label . '</a>';

        return $instance;
    }

    /**
     * Get the link HTML
     *
     * @author TB
     * @date 3.5.2025
     *
     * @return string
     */
    public function get() : string {
        return $this->_html;
    }

    /**
     * Render the link HTML
     *
     * @author TB
     * @date 3.5.2025
     *
     * @return void
     */
    public function render() : void {
        echo $this->_html;
    }

    /**
     * Build a link that opens a modal
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $link
     * @param string|null $label
     * @param array|null $params
     *
     * @return self
     */
    public static function modal(string $link, ?string $label = null, ?array $params = null): self {

        $label = $label ?: $link;

        $attributes = [];

        if (!empty($params['class'] ?? '')) {
            $attributes['class'] = $params['class'];
        }

        if (!empty($params['style'] ?? '')) {
            $attributes['style'] = $params['style'];
        }

        if (!empty($params['id'] ?? '')) {
            $attributes['id'] = $params['id'];
        }

        if (!empty($params['title'] ?? '')) {

            $attributes['title'] = $params['title'];

            if (!is_nempty_array($params['data'] ?? [])) {
                $params['data'] = [];
            }
            $params['data']['bs-toggle'] = 'tooltip';
        }

        if (is_nempty_array($params['data'] ?? [])) foreach ($params['data'] as $key => $val) {
            $attributes['data-' . $key] = $val;
        }

        $width = empty($width) ? 'false' : htmlspecialchars($width);
        $height = empty($height) ? 'false' : htmlspecialchars($height);
        $no_resize = empty($no_resize) ? 'false' : htmlspecialchars($no_resize);

        $pre_js    = $params['pre_js'] ?? '';
        $after_js  = $params['after_js'] ?? '';

        if (!empty($params['mode'])) {
            $pre_js .= " modal_mode = '" . addslashes($params['mode']) . "'; ";
        }

        if (!empty($params['size'])) {
            $pre_js .= " modal_size = '" . addslashes($params['size']) . "'; ";
        }

        $attributes['onclick'] = $pre_js . 'open_modal(\'' . $link . '\', ' . $width . ', ' . $height . ', ' . $no_resize . '); ' . $after_js;

        return self::a('javascript:;', $label, $attributes);
    }

}
