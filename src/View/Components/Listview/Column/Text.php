<?php

namespace Lumio\View\Components\Listview\Column;

use Lumio\View\Components\Listview\Column;

class Text extends Column {

    /**
     * Maximum length of the text
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var int|null
     */
    protected ?int $_truncate;

    /**
     * Text column
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $name
     * @param string $label
     * @param int|null $width
     * @param int|null $truncate
     * @param string|null $class
     *
     * @return void
     */
    public function __construct(string $name, string $label, ?int $width = null, ?int $truncate = null, ?string $class = null) {
        parent::__construct($name, $label, $width, $class);
        $this->_truncate = $truncate;
    }

    /**
     * Get HTML of the text value from given row
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param mixed $row
     *
     * @return string
     */
    public function get(mixed $row): string {

        $text = $row[$this->_name] ?? '';
        if (!is_scalar($text)) {
            return '';
        }

        $text = (string)$text;

        if ($this->_truncate && mb_strlen($text) > $this->_truncate) {
            $text = mb_substr($text, 0, $this->_truncate) . '...';
        }

        return htmlspecialchars($text);
    }

}