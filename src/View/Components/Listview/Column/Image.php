<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components;
use Lumio\View\Components\Listview\Column;

class Image extends Column {

    /**
     * Width of the image
     *
     * @var mixed
     */
    protected mixed $width;

    /**
     * Height of the image
     *
     * @var mixed
     */
    protected mixed $height;

    /**
     * Image column
     *
     * @param string $name
     * @param string $label
     * @param int|null $width
     * @param int|null $height
     * @param string|null $class
     *
     * @return void
     */
    public function __construct(string $name, string $label, ?int $width = null, ?int $height = null, ?string $class = null) {

        parent::__construct($name, $label, $class);

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get HTML code of the image
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get(mixed $row) : string {

        $src = $row[$this->_name] ?? '';
        if (empty($src)) {
            return '';
        }

        $style = [];

        if ($this->width) {

            $width = $this->width;
            if (is_numeric($width)) {
                $width .= 'px';
            }
            $style[] = 'width:' . $width;
        }

        if ($this->height) {

            $height = $this->height;
            if (is_numeric($height)) {
                $height .= 'px';
            }
            $style[] = 'height:' . $height;
        }

        return Components\Img::build(
            src: $src,
            attributes: [
                'style' => $style,
                'class' => $this->get_class(),
            ],
        )->get();
    }
}

