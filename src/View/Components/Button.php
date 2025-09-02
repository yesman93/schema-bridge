<?php

namespace Lumio\View\Components;

use Lumio\View\Component;

class Button extends Component {

    /**
     * HTML of the button
     *
     * @var string
     */
    private string $_html = '';

    /**
     * Build a button
     *
     * @param string $link
     * @param string $label
     * @param string|null $icon
     * @param bool $is_modal
     * @param string|null $modal_size
     * @param string|null $class
     * @param string|null $id
     * @param array|null $data
     *
     * @return self
     */
    public static function build(
        string $link,
        string $label,
        ?string $icon = null,
        bool $is_modal = false,
        ?string $modal_size = null,
        ?string $class = null,
        ?string $id = null,
        ?array $data = null
    ) : self {

        $instance = new self();

        if (!empty($icon) && self::_contains_icon_class($icon)) {
            $icon = '<i class="' . $icon . ' me-2"></i>';
        } else {
            $icon = '';
        }

        $label = $icon . $label;

        $params = [
            'class' => 'btn ' . ($class ?: 'btn-primary'),
        ];

        if (!empty($id)) {
            $params['id'] = $id;
        }

        if (is_nempty_array($data)) {
            $params['data'] = $data;
        }

        if ($is_modal) {

            if (!empty($modal_size)) {
                $params['size'] = $modal_size;
            }

            $btn = Link::modal($link, $label, $params);

        } else {
            $btn = Link::a($link, $label, $params);
        }

        $instance->_html = $btn->get();

        return $instance;
    }

    /**
     * Get HTML of the button
     *
     * @return string
     */
    public function get(): string {
        return $this->_html;
    }

    /**
     * Render the button
     *
     * @return void
     */
    public function render(): void {
        echo $this->get();
    }

}
