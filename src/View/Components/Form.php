<?php

namespace Lumio\View\Components;

use Lumio\Config;
use Lumio\DTO\View\FormReadableValue;
use Lumio\DTO\View\FormSetup;
use Lumio\DTO\View\FormInput;
use Lumio\Exceptions\LumioViewException;
use Lumio\IO\MessageBag;
use Lumio\View\Component;
use Lumio\Traits;

class Form extends Component {

    use Traits\IO\Message;
    use Traits\View\Master;

    /**
     * Form enctype - application/x-www-form-urlencoded
     *
     * @var string
     */
    public const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';

    /**
     * Form enctype - application/form-data
     *
     * @var string
     */
    public const ENCTYPE_MULTIPART = 'multipart/form-data';


    /**
     * Form action
     *
     * @var string
     */
    protected string $_form_action = '';

    /**
     * Form enctype
     *
     * @var string
     */
    protected string $_enctype = '';

    /**
     * Form wrapper class
     *
     * @var string
     */
    protected string $_class_wrapper = '';

    /**
     * Form card class
     *
     * @var string
     */
    protected string $_class_card = '';

    /**
     * Form header class
     *
     * @var string
     */
    protected string $_class_header = '';

    /**
     * Form body class
     *
     * @var string
     */
    protected string $_class_body = '';

    /**
     * Form class
     *
     * @var string
     */
    protected string $_class = '';

    /**
     * Form ID
     *
     * @var string
     */
    protected string $_id = '';

    /**
     * Form prefill data
     *
     * @var array
     */
    protected array $_data = [];

    /**
     * Form prefill data ID key
     *
     * @var string
     */
    protected string $_data_id = '';

    /**
     * Stored readable selected/filled input values of the form, available at the end of the render
     *
     * @var array
     */
    private array $_readable_values = [];

    /**
     * Indicates if it is the filter form.
     * If enabled, prefix by config `app.filter.fields_prefix` will be prepended to form field names
     *
     * @var bool
     */
    private static bool $_is_filter = false;

    /**
     * Builds the form components end renders the opening HTML
     *
     * @param FormSetup|null $form_setup
     *
     * @return self
     */
    public static function build(?FormSetup $form_setup = null): self {

        if (empty($form_setup)) {
            $form_setup = new FormSetup();
        }

        $instance = new self();

        $instance->_form_action = $form_setup->get_action();
        $instance->_enctype = $form_setup->get_enctype() ?? '';
        $instance->_class_wrapper = $form_setup->get_class_wrapper();
        $instance->_class_card = $form_setup->get_class_card();
        $instance->_class_header = $form_setup->get_class_header();
        $instance->_class_body = $form_setup->get_class_body();
        $instance->_class = $form_setup->get_class();
        $instance->_id = $form_setup->get_id();

        $instance->_data = $form_setup->get_data();
        $instance->_data_id = $form_setup->get_data_id();

        $is_card = $form_setup->is_card();
        if ($is_card === null) {

            if (self::$_master == self::MASTER_MODAL) {
                $is_card = false;
            } else {
                $is_card = true;
            }
        }

        $show_title = $form_setup->show_title();
        if ($show_title === null) {

            if (self::$_master == self::MASTER_MODAL) {
                $show_title = false;
            } else {
                $show_title = true;
            }
        }

        $title = self::get_title();
        $action_uri = $instance->_form_action ?: self::_get_action_uri();

        $enctype = self::_not_empty_to_attr('enctype', $instance->_enctype);
        $method = self::_not_empty_to_attr('method', 'post');
        $action = self::_not_empty_to_attr('action', $action_uri);
        $class = self::_not_empty_to_attr('class', $instance->_class);
        $id = self::_not_empty_to_attr('id', $instance->_id);

        $html = '<div class="form-wrapper ' . $instance->_class_wrapper . '">';

        if (!self::$_is_filter) {

            $html .= '<div class="' . ($is_card ? 'card' : '') . ' ' . $instance->_class_card . '">';

            if ($show_title) {

                $html .= '
                    <div class="card-header ' . $instance->_class_header . '">
                        <h3 class="card-title mb-0">' . htmlspecialchars($title) . '</h3>
                    </div>';
            }

            $html .= '<div class="card-body ' . $instance->_class_body . '">';
        }

        $html .= '<form ' . $action . ' ' . $method . ' ' . $enctype . ' ' . $class . ' ' . $id . '>';

        $csrf_name = htmlspecialchars(self::$_csrf_field);
        $html .= '
        <input type="hidden" id="' . $csrf_name . '" name="' . $csrf_name . '" value="' . self::$_csrf_token . '">';

        // Hidden ID field for data identificator if present
        if (!empty($instance->_data_id) && isset($instance->_data[$instance->_data_id])) {
            $html .= '<input type="hidden" id="' . htmlspecialchars($instance->_data_id) . '" name="' . htmlspecialchars($instance->_data_id) . '" value="' . htmlspecialchars($instance->_data[$instance->_data_id]) . '">';
        }

        // Render global messages if present
        foreach (MessageBag::get_errors('') as $message) {
            $html .= '<div class="alert alert-danger py-2 text-danger"><i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($message) . '</div>';
        }

        echo $html;

        return $instance;
    }

    /**
     * Resolve the input prefill value
     *
     * @param FormInput $input
     *
     * @return mixed
     */
    private function _resolve_value(FormInput $input): mixed {

        $name = $input->get_name();

        $value = $input->get_default();

        if ($input->is_force_default()) {
            return $value;
        }

        if (is_nempty_array($this->_data) && isset($this->_data[$name])) {
            $value = $this->_data[$name];
        }

        $value_get = self::$_request->get($name);
        $value_post = self::$_request->post($name);

        if ($value_get !== null) {
            $value = $value_get;
        }

        if ($value_post !== null) {
            $value = $value_post;
        }

        if (self::$_is_filter) {

            $value_filter = self::$_request->filter($name);
            if ($value_filter !== null) {
                $value = $value_filter;
            }
        }

        return $value;
    }

    /**
     * Returns class for select plugin initialization by default select plugin - only if required plugin source JS is loaded
     *
     * @return string
     */
    private function _get_select_default_plugin_class() : string {

        try {
            $default_select_plugin = Config::get('app.view.components.form.default_select_plugin');
        } catch (\Throwable $e) {
            $default_select_plugin = '';
        }

        if (empty($default_select_plugin)) {
            return '';
        }

        try {
            $required = Config::get('app.view.components.form.select_plugin_required.' . $default_select_plugin);
        } catch (\Throwable $e) {
            $required = [];
        }

        if (empty($required)) {
            return '';
        }

        try {
            $assets_js = Config::get('app.view.assets.js');
        } catch (\Throwable $e) {
            $assets_js = [];
        }

        try {
            $assets_master_js = Config::get('app.view.assets.' . ucfirst(self::$_master) . '.js');
        } catch (\Throwable $e) {
            $assets_master_js = [];
        }

        $assets = array_merge($assets_js, $assets_master_js);
        if (empty($assets)) {
            return '';
        }

        foreach ($assets as $asset) {

            foreach ($required as $key => $req) {

               if (strpos($asset, $req) !== false) {
                    unset($required[$key]);
               }
            }

            if (empty($required)) {
                break;
            }
        }

        if (empty($required)) {

            try {
                return Config::get('app.view.components.form.select_plugin_classes.' . $default_select_plugin);
            } catch (\Throwable $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * Render the input HTML
     *
     * @param string $type
     * @param FormInput $input
     *
     * @return void
     *
     * @throws LumioViewException
     */
    private function _render_input(string $type, FormInput $input): void {

        $name = $input->get_name();
        $label = $input->get_label();
        $is_multiple = $input->is_multiple();
        $icon = $input->get_icon() ?? '';
        $addon_prepend = $input->get_addon_prepend() ?? '';
        $addon_append = $input->get_addon_append() ?? '';
        $class_input_group = $input->get_class_input_group() ?? '';

        $errors = MessageBag::get_errors($name);
        $has_errors = !empty($errors);

        $filter_prefix = '';
        if (self::$_is_filter) {

            try {
                $filter_prefix = Config::get('app.filter.fields_prefix');
            } catch (\Throwable $e) {}
        }

        $name = $filter_prefix . $name;

        $attr = [
            'name' => $name,
        ];

        $value = $this->_resolve_value($input);
        $id = empty($input->get_id()) ? $name : $input->get_id();

        $is_select = $type == 'select';
        $is_select_multiple = $is_select && $is_multiple;
        $is_choices = in_array($type, ['checkbox', 'radio']);
        $is_checkbox = $type == 'checkbox';

        if ($is_select) {

            if ($is_multiple) {
                $attr['multiple'] = 'multiple';
            }

        } else {
            $attr['type'] = $type;
        }

        if ($is_choices) {
            $attr['class'] = 'form-check-input ' . $input->get_class();
        } else {
            $attr['id'] = $id;
            $attr['class'] = 'form-control ' . $input->get_class() . ($has_errors ? ' is-invalid' : '');
        }

        if ($is_select_multiple || $is_checkbox) {

            if (!str_contains($attr['name'], '[]')) {
                $attr['name'] .= '[]';
            }
        }

        if (!$is_select && !$is_choices) {
            $attr['value'] = $value;
        }

        if ($input->is_readonly()) {
            $attr['readonly'] = 'readonly';
        }

        foreach (($input->get_data() ?? []) as $k => $v) {
            $attr['data-' . $k] = $v;
        }

        if (self::$_is_filter && $input->is_inline() === null) {
            $input->set_inline(true);
        }

        if (self::$_is_filter && empty($input->get_class_col())) {
            $input->set_class_col('col-md-4 col-lg-3');
        }

        $required = $input->is_required() ? '<span class="text-danger">*</span>' : '';
        $icon = $icon && self::_contains_icon_class($icon) ? '<i class="' . htmlspecialchars($icon) . ' me-2"></i>' : '';

        $input_group_start = '';
        $input_group_end = '';
        if (!empty($addon_prepend) || !empty($addon_append)) {
            $input_group_start = '<div class="input-group ' . $class_input_group . '">';
            $input_group_end = '</div>';
        }

        if (!empty($addon_prepend)) {

            if (self::_contains_icon_class($addon_prepend)) {
                $addon_prepend = '<i class="' . $addon_prepend . '"></i>';
            }

            $addon_prepend = '<span class="input-group-text">' . $addon_prepend . '</span>';
        }

        if (!empty($addon_append)) {

            if (self::_contains_icon_class($addon_append)) {
                $addon_append = '<i class="' . $addon_append . '"></i>';
            }

            $addon_append = '<span class="input-group-text">' . $addon_append . '</span>';
        }

        if ($is_select) {

            $class_plugin = $this->_get_select_default_plugin_class();
            if (!empty($class_plugin)) {
                $attr['class'] .= ' ' . $class_plugin;
            }

            $input_html = '
            <select ' . $this->_get_attr($attr) . '>';

            foreach ($input->get_source() as $option) {

                $option_value = htmlspecialchars($option['value'] ?? '');
                $option_label = htmlspecialchars($option['label'] ?? '');

                if ($is_multiple) {
                    $selected = in_array($option_value, $value ?? []) ? self::_not_empty_to_attr('selected', 'selected') : '';
                } else {
                    $selected = ($value == $option_value) ? self::_not_empty_to_attr('selected', 'selected') : '';
                }

                $input_html .= '
                <option value="' . $option_value . '"' . $selected . '>' . $option_label . '</option>';
            }

            $input_html .= '
            </select>';

        } else if ($is_choices) {

            $input_html = '';
            foreach ($input->get_source() as $choice) {

                $choice_value = htmlspecialchars($choice['value'] ?? '');
                $choice_label = htmlspecialchars($choice['label'] ?? '');

                if ($is_checkbox) {
                    $checked = in_array($choice_value, $value ?? []) ? self::_not_empty_to_attr('checked', 'checked') : '';
                } else {
                    $checked = ($value == $choice_value) ? self::_not_empty_to_attr('checked', 'checked') : '';
                }

                $choice_id = self::_not_empty_to_attr('id', $name . '_' . $choice_value);
                $choice_for = self::_not_empty_to_attr('for', $name . '_' . $choice_value);
                $choice_value = self::_not_empty_to_attr('value', $choice_value);

                $input_html .= '
                <div class="form-check">
                    <input ' . $this->_get_attr($attr) . ' ' . $choice_id . ' ' . $choice_value . ' ' . $checked . '>
                    <label class="form-check-label" ' . $choice_for . '>
                        ' . $choice_label . '
                    </label>
                </div>';
            }

        } else {

            $input_html = '<input ' . $this->_get_attr($attr) . '>';
        }

        $errors_html = '';
        if ($has_errors) {

            $errors_html .= '<div class="invalid-feedback">';
            foreach (MessageBag::get_errors($name) as $error) {
                $errors_html .= '<div><i class="fas fa-exclamation-circle me-1"></i>' . htmlspecialchars($error) . '</div>';
            }
            $errors_html .= '</div>';
        }

        $html = '<div class="' . $input->get_class_col() . '">';
        $html .= '<div class="form-group ' . $input->get_class_group() . '">';
        $html .= '<label for="' . htmlspecialchars($name) . '" class="form-label">' . $icon . htmlspecialchars($label) . $required . '</label>';
        $html .= $input_group_start;
        $html .= $addon_prepend;
        $html .= $input_html;
        $html .= $addon_append;
        $html .= $input_group_end;
        $html .= $errors_html;

        $html .= '</div>';
        $html .= '</div>';

        echo $html;

        $this->_store_readable_value($input, $type);
    }

    /**
     * Render the text input
     *
     * @param FormInput $input
     *
     * @return void
     */
    public function text(FormInput $input): void {
        $this->_render_input('text', $input);
    }

    /**
     * Render the password input
     *
     * @param FormInput $input
     *
     * @return void
     */
    public function password(FormInput $input): void {

        $input->set_addon_append('fal fa-eye password-input-toggle');

        $this->_render_input('password', $input);
    }

    /**
     * Render the color input
     *
     * @param FormInput $input
     *
     * @return void
     */
    public function color(FormInput $input): void {
        $this->_render_input('color', $input);
    }

    /**
     * Render the hidden input
     *
     * @param FormInput $input
     *
     * @return void
     */
    public function hidden(FormInput $input): void {

        $name = $input->get_name();
        $id = empty($input->get_id()) ? $name : $input->get_id();
        $value = $this->_resolve_value($input);

        $id = self::_not_empty_to_attr('id', $id);
        $name = self::_not_empty_to_attr('name', $name);

        echo '<input type="hidden" ' . $id . ' ' . $name . ' value="' . htmlspecialchars($value) . '">';
    }

    /**
     * Render the file input
     *
     * @param FormInput $input
     *
     * @return void
     *
     * @throws LumioViewException
     */
    public function file(FormInput $input): void {

        if ($this->_enctype != self::ENCTYPE_MULTIPART) {
            ob_clean();
            throw new LumioViewException(__tx('Forms containing file inputs must have enctype set to multipart/form-data'));
        }

        $this->_render_input('file', $input);
    }

    /**
     * Render the date input
     *
     * @param FormInput $input
     *
     * @return void
     */
    public function date(FormInput $input): void {
        $this->_render_input('date', $input);
    }

    public function time(FormInput $input): void {
        $this->_render_input('time', $input);
    }

    public function select(FormInput $input): void {


        $this->_render_input('select', $input);

    }

    public function radio(FormInput $input): void {

        $this->_render_input('radio', $input);

    }

    public function checkbox(FormInput $input): void {


        $this->_render_input('checkbox', $input);

    }

    public function textarea(FormInput $input): void
    {
        $name = $input->get_name();
        $label = $input->get_label();
        $value = $this->_resolve_value($input);
        $is_wysiwyg = $input->is_wysiwyg();
        $has_error = !empty(self::get_errors($name));

        $class = $input->get_class_form() . ($has_error ? ' is-invalid' : '');

        echo '<div class="' . $input->get_class_col() . '"><div class="form-group">';
        echo '<label for="' . htmlspecialchars($name) . '" class="form-label">' . htmlspecialchars($label) . '</label>';
        echo '<textarea name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" class="' . htmlspecialchars($class) . '">' . htmlspecialchars($value) . '</textarea>';

        if ($is_wysiwyg) {
            echo '<input type="hidden" name="_wysiwyg[]" value="' . htmlspecialchars($name) . '">';
        }

        if ($has_error) {
            echo '<ul class="invalid-feedback">';
            foreach (self::get_errors($name) as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
        }
        echo '</div></div>';
    }

    /**
     * Render the submit button and close the form
     *
     * @param FormInput|null $input
     *
     * @return void
     */
    public function submit(?FormInput $input = null): void {

        if ($input === null) {
            $input = new FormInput();
        }

        $name = $input->get_name();
        $label = $input->get_label();
        $align = $input->get_align();
        $class = $input->get_class();

        if (empty($name)) {
            $name = 'submit-' . self::get_action();
        } else {
            $name = 'submit-' . $name;
        }

        $id = empty($input->get_id()) ? $name : $input->get_id();

        if (empty($label)) {
            $label = __tx('Save');
        }

        if (empty($align)) {
            $align = 'end';
        }

        $name = self::_not_empty_to_attr('name', $name);
        $id = self::_not_empty_to_attr('id', $id);
        $class = self::_not_empty_to_attr('class', 'btn btn-primary ' . $class);

        $html = '<div class="d-flex justify-content-' . htmlspecialchars($align) . ' mt-4">';
        $html .= '<button type="submit" ' . $name . ' ' . $id . ' ' . $class . ' value="1">' . htmlspecialchars($label) . '</button>';
        $html .= '</div>';

        if (!self::$_is_filter) {
            $html .= '</form></div></div>'; // Close form, .card-body, .card
        }

        $html .= '</div>'; // Close .form-wrapper

        echo $html;
    }

    /**
     * Set if the form is a filter form.
     * If enabled, prefix by config `app.filter.fields_prefix` will be prepended to form field names
     *
     * @param bool $enable
     *
     * @return void
     */
    public static function filter(bool $enable = true): void {
        self::$_is_filter = $enable;
    }

    /**
     * Start a row
     *
     * @param string|null $class
     *
     * @return void
     */
    public function row(?string $class = null) : void {

        $class = $class ?? '';

        echo '<div class="row ' . htmlspecialchars($class) . '">';
    }

    /**
     * End a row
     *
     * @return void
     */
    public function end_row(): void {
        echo '</div>';
    }

    /**
     * Get readable values
     *
     * @return array
     */
    public function get_readable_values(): array {
        return $this->_readable_values;
    }

    /**
     * Store readable value for the given input
     *
     * @param FormInput $input
     * @param string $type
     *
     * @return void
     */
    private function _store_readable_value(FormInput $input, string $type): void {

        $name = $input->get_name();
        $label = $input->get_label();
        $value = $this->_resolve_value($input);

        if (self::$_is_filter) {

            try {
                $filter_prefix = Config::get('app.filter.fields_prefix');
                $name = $filter_prefix . $name;
            } catch (\Throwable $e) {}
        }

        if (!in_array($type, ['select', 'checkbox', 'radio'])) {
            $this->_readable_values[$name] = new FormReadableValue($name, $value, $label);
            return;
        }

        $source = $input->get_source();

        if ($input->is_multiple()) {

            $value_lookup = array_flip($value); // performace increase 1 - isset() is faster than in_array(), especially on large arrays
            $labels = []; // performace increase 2 - instead of repeated string concatenation collect the labels in an array and the connect
            foreach ($source as $item) {
                if (isset($value_lookup[$item['value1']])) { // performace increase 1 - isset() is faster than in_array(), especially on large arrays
                    $labels[] = $item['label']; // performace increase 2 - instead of repeated string concatenation collect the labels in an array and the connect
                }
            }
            $value_readable = implode(', ', $labels); // performace increase 2 - instead of repeated string concatenation collect the labels in an array and the connect

            $readable_value = new FormReadableValue($name, $value_readable, $label);

        } else {

            foreach ($source as $item) {

                if ($item['value'] == $value) {
                    $readable_value = new FormReadableValue($name, $item['label'], $label);
                    break;
                }
            }
        }

        if (isset($readable_value)) {
            $this->_readable_values[$name] = $readable_value;
        }
    }

}
