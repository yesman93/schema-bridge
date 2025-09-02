<?php

namespace Lumio\DTO\View;

use Lumio\Exceptions\LumioViewException;

class FormInput {

    /**
     * Input name
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_name = '';

    /**
     * Input label
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_label = '';

    /**
     * Input ID
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_id = '';

    /**
     * Input source
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var array
     */
    private array $_source = [];

    /**
     * Input searchable
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    private bool $_searchable = false;

    /**
     * Input default value
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var mixed
     */
    private mixed $_default = null;

    /**
     * Force default value
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    private bool $_force_default = false;

    /**
     * Input required
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    private bool $_required = false;

    /**
     * Input disabled
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    private bool $_disabled = false;

    /**
     * Input readonly
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    private bool $_readonly = false;

    /**
     * Input multiple
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    private bool $_multiple = false;

    /**
     * Input placeholder
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_placeholder = '';

    /**
     * Input class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_class = '';

    /**
     * Whether the input is in a row with other inputs
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool|null
     */
    private ?bool $_inline = null;

    /**
     * Input col-* class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_class_col = '';

    /**
     * Input .form-group class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_class_group = '';

    /**
     * Input label class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_class_label = '';

    /**
     * Input icon
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string
     */
    private string $_icon = '';

    /**
     * Input is WYSIWYG text
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var bool
     */
    private bool $_is_wysiwyg = false;

    /**
     * Input align (for submit)
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string|null
     */
    private ?string $_align = null;

    /**
     * Input data attributes
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var array|null
     */
    private ?array $_data = [];

    /**
     * Input prepend add-on
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string|null
     */
    private ?string $_addon_prepend = null;

    /**
     * Input append add-on
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string|null
     */
    private ?string $_addon_append = null;

    /**
     * Input group class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var string|null
     */
    private ?string $_class_input_group = null;

    /**
     * DTO for form input definition
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $source
     * @param bool $searchable
     * @param mixed|null $default
     * @param bool $force_default
     * @param bool $required
     * @param bool $disabled
     * @param bool $readonly
     * @param bool $multiple
     * @param string $placeholder
     * @param string $class
     * @param bool|null $inline
     * @param string $class_col
     * @param string $class_group
     * @param string $class_label
     * @param string $icon
     * @param bool $is_wysiwyg
     * @param array $data
     * @param string|null $align
     * @param string|null $addon_prepend
     * @param string|null $addon_append
     * @param string|null $class_input_group
     *
     * @return void
     */
    public function __construct(
        string $name = '',
        string $label = '',
        string $id = '',
        array $source = [],
        bool $searchable = false,
        mixed $default = null,
        bool $force_default = false,
        bool $required = false,
        bool $disabled = false,
        bool $readonly = false,
        bool $multiple = false,
        string $placeholder = '',
        string $class = '',
        ?bool $inline = null,
        string $class_col = '',
        string $class_group = 'mb-3',
        string $class_label = '',
        string $icon = '',
        bool $is_wysiwyg = false,
        array $data = [],
        ?string $align = null,
        ?string $addon_prepend = null,
        ?string $addon_append = null,
        ?string $class_input_group = null
    ) {

        $this->_name = $name;
        $this->_label = $label;
        $this->_id = $id;
        $this->_source = $source;
        $this->_searchable = $searchable;
        $this->_default = $default;
        $this->_force_default = $force_default;
        $this->_required = $required;
        $this->_disabled = $disabled;
        $this->_readonly = $readonly;
        $this->_multiple = $multiple;
        $this->_placeholder = $placeholder;
        $this->_class = $class;
        $this->_inline = $inline;
        $this->_class_col = $class_col;
        $this->_class_group = $class_group;
        $this->_class_label = $class_label;
        $this->_icon = $icon;
        $this->_is_wysiwyg = $is_wysiwyg;
        $this->_data = $data;
        $this->_align = $align;
        $this->_addon_prepend = $addon_prepend;
        $this->_addon_append = $addon_append;
        $this->_class_input_group = $class_input_group;

        $this->_check_source($this->_source);

        $this->_data['show-search'] = $this->_searchable ? 1 : 0;
    }

    /**
     * Get input name
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

    /**
     * Get input label
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_label(): string {
        return $this->_label;
    }

    /**
     * Get input ID
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_id(): string {
        return $this->_id;
    }

    /**
     * Get input source
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return array
     */
    public function get_source(): array {
        return $this->_source;
    }

    /**
     * Get if input is searchable
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public function is_searchable(): bool {
        return $this->_searchable;
    }

    /**
     * Get input default value
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return mixed
     */
    public function get_default(): mixed {
        return $this->_default;
    }

    /**
     * Get if input is forced default value
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public function is_force_default(): bool {
        return $this->_force_default;
    }

    /**
     * Get if input is required
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public function is_required(): bool {
        return $this->_required;
    }

    /**
     * Get if input is disabled
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public function is_disabled(): bool {
        return $this->_disabled;
    }

    /**
     * Get if input is readonly
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public function is_readonly(): bool {
        return $this->_readonly;
    }

    /**
     * Get if input is multiple
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public function is_multiple(): bool {
        return $this->_multiple;
    }

    /**
     * Get input placeholder
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_placeholder(): string {
        return $this->_placeholder;
    }

    /**
     * Get input class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_class(): string {
        return $this->_class;
    }

    /**
     * Get if input is in a row with other inputs
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool|null
     */
    public function is_inline(): ?bool {
        return $this->_inline;
    }

    /**
     * Get input col-* class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_class_col(): string {
        return $this->_class_col;
    }

    /**
     * Get input .form-group class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_class_group(): string {
        return $this->_class_group;
    }

    /**
     * Get input label class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_class_label(): string {
        return $this->_class_label;
    }

    /**
     * Get input icon
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string
     */
    public function get_icon(): string {
        return $this->_icon;
    }

    /**
     * Get if input is WYSIWYG text
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    public function is_wysiwyg(): bool {
        return $this->_is_wysiwyg;
    }

    /**
     * Get input align (for submit)
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string|null
     */
    public function get_align(): ?string {
        return $this->_align;
    }

    /**
     * Get input data attributes
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return array|null
     */
    public function get_data(): ?array {
        return $this->_data;
    }

    /**
     * Get input append add-on
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string|null
     */
    public function get_addon_append(): ?string {
        return $this->_addon_append;
    }

    /**
     * Get input prepend add-on
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string|null
     */
    public function get_addon_prepend(): ?string {
        return $this->_addon_prepend;
    }

    /**
     * Get input group class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return string|null
     */
    public function get_class_input_group(): ?string {
        return $this->_class_input_group;
    }

    /**
     * Set input name
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $name
     *
     * @return void
     */
    public function set_name(string $name): void {
        $this->_name = $name;
    }

    /**
     * Set input label
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $label
     *
     * @return void
     */
    public function set_label(string $label): void {
        $this->_label = $label;
    }

    /**
     * Set input ID
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $id
     *
     * @return void
     */
    public function set_id(string $id): void {
        $this->_id = $id;
    }

    /**
     * Set input source
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param array $source
     *
     * @return void
     */
    public function set_source(array $source): void {
        $this->_source = $source;
    }

    /**
     * Set if input is searchable
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param bool $searchable
     *
     * @return void
     */
    public function set_searchable(bool $searchable): void {
        $this->_searchable = $searchable;
    }

    /**
     * Set input default value
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param mixed $default
     *
     * @return void
     */
    public function set_default(mixed $default): void {
        $this->_default = $default;
    }

    /**
     * Set if input is forced default value
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param bool $force_default
     *
     * @return void
     */
    public function set_force_default(bool $force_default): void {
        $this->_force_default = $force_default;
    }

    /**
     * Set if input is required
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param bool $required
     *
     * @return void
     */
    public function set_required(bool $required): void {
        $this->_required = $required;
    }

    /**
     * Set if input is disabled
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param bool $disabled
     *
     * @return void
     */
    public function set_disabled(bool $disabled): void {
        $this->_disabled = $disabled;
    }

    /**
     * Set if input is readonly
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param bool $readonly
     *
     * @return void
     */
    public function set_readonly(bool $readonly): void {
        $this->_readonly = $readonly;
    }

    /**
     * Set input placeholder
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $placeholder
     *
     * @return void
     */
    public function set_placeholder(string $placeholder): void {
        $this->_placeholder = $placeholder;
    }

    /**
     * Set input class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $class
     *
     * @return void
     */
    public function set_class(string $class): void {
        $this->_class = $class;
    }

    /**
     * Set if input is in a row with other inputs
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param bool|null $inline
     *
     * @return void
     */
    public function set_inline(?bool $inline): void {
        $this->_inline = $inline;
    }

    /**
     * Set input col-* class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $class_col
     *
     * @return void
     */
    public function set_class_col(string $class_col): void {
        $this->_class_col = $class_col;
    }

    /**
     * Set input .form-group class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $class_group
     *
     * @return void
     */
    public function set_class_group(string $class_group): void {
        $this->_class_group = $class_group;
    }

    /**
     * Set input label class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $class_label
     *
     * @return void
     */
    public function set_class_label(string $class_label): void {
        $this->_class_label = $class_label;
    }

    /**
     * Set input icon
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $icon
     *
     * @return void
     */
    public function set_icon(string $icon): void {
        $this->_icon = $icon;
    }

    /**
     * Set if input is WYSIWYG text
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param bool $is_wysiwyg
     *
     * @return void
     */
    public function set_is_wysiwyg(bool $is_wysiwyg): void {
        $this->_is_wysiwyg = $is_wysiwyg;
    }

    /**
     * Set input align (for submit)
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string|null $align
     *
     * @return void
     */
    public function set_align(?string $align): void {
        $this->_align = $align;
    }

    /**
     * Set input data attributes
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param array|null $data
     *
     * @return void
     */
    public function set_data(?array $data): void {
        $this->_data = $data;
    }

    /**
     * Set input append add-on
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string|null $addon_append
     *
     * @return void
     */
    public function set_addon_append(?string $addon_append): void {
        $this->_addon_append = $addon_append;
    }

    /**
     * Set input prepend add-on
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string|null $addon_prepend
     *
     * @return void
     */
    public function set_addon_prepend(?string $addon_prepend): void {
        $this->_addon_prepend = $addon_prepend;
    }

    /**
     * Set input group class
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string|null $class_input_group
     *
     * @return void
     */
    public function set_class_input_group(?string $class_input_group): void {
        $this->_class_input_group = $class_input_group;
    }

    /**
     * Check if source array is valid
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param array $source
     *
     * @return void
     *
     * @throws LumioViewException
     */
    private function _check_source(array $source): void {

        if (!is_nempty_array($source)) {
            return;
        }

        foreach ($source as $item) {

            if (!array_key_exists('value', $item) || !array_key_exists('label', $item)) {
                ob_clean();
                throw new LumioViewException('Source array for input "' . $this->_name . '" must contain "value" and "label" keys');
            }
        }
    }

}
