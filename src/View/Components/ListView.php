<?php

namespace Lumio\View\Components;

use Lumio\Config;
use Lumio\DTO\Model\Sorting;
use Lumio\DTO\View\FormInput;
use Lumio\IO\URIParamsParser;
use Lumio\View\Component;
use Lumio\View\Components\ListView\Action;
use Lumio\View\Components\ListView\Column;
use Throwable;

class ListView extends Component {

    /**
     * Columns
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var array
     */
    private array $_columns = [];

    /**
     * Buttons
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var array
     */
    private array $_buttons = [];

    /**
     * Actions
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var array
     */
    private array $_actions = [];

    /**
     * Data for rendering the rows
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var array|null
     */
    private ?array $_data = null;

    /**
     * Text to be rendered if there is no data
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string|null
     */
    private ?string $_empty_text = null;

    /**
     * Filter name
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string|null
     */
    private ?string $_filter_name = null;

    /**
     * Filter enabled
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var bool
     */
    private bool $_filter = false;

    /**
     * Search enabled
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var bool
     */
    private bool $_search = false;

    /**
     * Pagination enabled
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var bool
     */
    private bool $_paginate = false;

    /**
     * Show title
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var bool
     */
    private bool $_show_title = true;

    /**
     * Title
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string|null
     */
    private ?string $_listview_title = null;

    /**
     * Export to XLS enabled
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var bool
     */
    private bool $_export_xls = false;

    /**
     * Export to CSV enabled
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var bool
     */
    private bool $_export_csv = false;

    /**
     * Checkers enabled
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var bool
     */
    private bool $_checkers = false;

    /**
     * Key for retrieving value from the row for checker value
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string|null
     */
    private ?string $_checker_key = null;

    /**
     * Checkers input name
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var string|null
     */
    private ?string $_checker_name = null;

    /**
     * Summary row columns
     *
     * @author TB
     * @date 3.5.2025
     *
     * @var array
     */
    private array $_summary_columns = [];

    /**
     * Applied filters (stored readable values from the filter form)
     *
     * @author TB
     * @date 4.5.2025
     *
     * @var array
     */
    private array $_applied_filters = [];

    /**
     * Current full URI including page number and applied filters
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var string
     */
    private string $_uri = '';

    /**
     * Show the share button
     *
     * @author TB
     * @date 7.5.2025
     *
     * @var bool
     */
    private bool $_share = true;

    /**
     * Create the listview component instance
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param array|null $data
     *
     * @return Listview
     */
    public static function build(?array $data = null) : self {

        $instance = new self();

        $instance->title(self::get_title() ?? '')
                ->empty_text(__tx('No data found'))
                ->paginate(true)
                ->show_title(true)
                ->share(true);

        if (!empty($data)) {
            $instance->data($data);
        }

        $instance->uri();

        return $instance;
    }

    /**
     * Set the URI
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param string|null $uri
     *
     * @return self
     */
    public function uri(?string $uri = null) : self {

        if ($uri !== null) {
            $this->_uri = $uri;
        } else {
            $this->_uri = self::_get_action_uri();
        }

        return $this;
    }

    /**
     * Set the data for listview
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param array $rows
     *
     * @return self
     */
    public function data(array $rows): self {
        $this->_data = $rows;
        return $this;
    }

    /**
     * Add a column to listview
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param Column $column
     *
     * @return self
     */
    public function add_column(Column $column): self {
        $this->_columns[] = $column;
        return $this;
    }

    /**
     * Add a button to listview
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param Button $button
     *
     * @return self
     */
    public function button(Button $button): self {
        $this->_buttons[] = $button;
        return $this;
    }

    /**
     * Add actions to listview
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param Action ...$actions
     *
     * @return self
     */
    public function actions(Action ...$actions): self {
        $this->_actions = $actions;
        return $this;
    }

    /**
     * Set the title of listview
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $title
     *
     * @return self
     */
    public function title(string $title): self {
        $this->_listview_title = $title;
        return $this;
    }

    /**
     * Set whether to show the title
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param bool $show
     *
     * @return self
     */
    public function show_title(bool $show): self {
        $this->_show_title = $show;
        return $this;
    }

    /**
     * Set whether to enable filter
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param bool $enable
     *
     * @return self
     */
    public function filter(bool $enable = true): self {
        $this->_filter = $enable;
        return $this;
    }

    /**
     * Set custom filter name
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $name
     *
     * @return self
     */
    public function filter_name(string $name): self {
        $this->_filter_name = $name;
        return $this;
    }

    /**
     * Set whether to enable search
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param bool $enable
     *
     * @return self
     */
    public function search(bool $enable = true): self {
        $this->_search = $enable;
        return $this;
    }

    /**
     * Set whether to enable pagination
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param bool $enable
     *
     * @return self
     */
    public function paginate(bool $enable = true): self {
        $this->_paginate = $enable;
        return $this;
    }

    /**
     * Set text to be displayed when there is no data
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string $text
     *
     * @return self
     */
    public function empty_text(string $text): self {
        $this->_empty_text = $text;
        return $this;
    }

    /**
     * Set whether to enable export to XLS
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param bool $enable
     *
     * @return self
     */
    public function export2xls(bool $enable = true): self {
        $this->_export_xls = $enable;
        return $this;
    }

    /**
     * Set whether to enable export to CSV
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param bool $enable
     *
     * @return self
     */
    public function export2csv(bool $enable = true): self {
        $this->_export_csv = $enable;
        return $this;
    }

    /**
     * Set whether to enable checkers.
     * If enabled, specify key for value from the row and name of the input.
     * If the name is not given, the key will be used as input name
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param bool $enable
     * @param string|null $key
     * @param string|null $name
     *
     * @return self
     */
    public function checkers(bool $enable, ?string $key = null, ?string $name = null): self {
        $this->_checkers = $enable;
        $this->_checker_key = $key;
        $this->_checker_name = $name ?? $key;
        return $this;
    }

    /**
     * Set whether to enable summary row.
     * If enabled, specify columns for which the summary will be calculated
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param string ...$columns
     *
     * @return self
     */
    public function summary(string ...$columns): self {
        $this->_summary_columns = $columns;
        return $this;
    }

    /**
     * Set whether to show the share button
     *
     * @author TB
     * @date 7.5.2025
     *
     * @param bool $enable
     *
     * @return self
     */
    public function share(bool $enable = true): self {
        $this->_share = $enable;
        return $this;
    }

    /**
     * Get link for sorting based on the given sorting object
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param Sorting $sorting
     *
     * @return string
     */
    private static function _get_sorting_link(Sorting $sorting) : string {

        return URIParamsParser::build(
            controller: self::$_controller,
            action: self::$_action,
            request: self::$_request,
            realm: self::$_realm,
            params: self::$_params,
            sorting: $sorting,
        );
    }

    /**
     * Render the listview
     *
     * @author TB
     * @date 3.5.2025
     *
     * @return void
     */
    public function render(): void {

        $html = '
        <div class="card listview-wrapper">';

        $html .= '
            <div class="card-header">';
        $html .= '
                <div class="container-fluid px-1">';
        $html .= '
                    <div class="row gx-1">';
        $html .= '
                        <div class="col-12 col-xl-2">';

        if ($this->_show_title && $this->_listview_title) {

            $html .= '
                            <h3 class="card-title">' . htmlspecialchars($this->_listview_title) . '</h3>';
        }

        $html .= '
                        </div>';
        $html .= '
                        <div class="col-12 col-xl-10">';
        $html .= '
                            <div class="row gx-1 justify-content-end">';

        if (!empty($this->_buttons)) {

            $html .= '
                                <div class="col-12 col-md-7 col-lg-8 text-start text-xl-end">';
            $html .= '
                                    <div class="d-flex flex-wrap justify-content-end gap-2 ' . ($this->_search ? 'pe-3' : '') . '">';

            foreach ($this->_buttons as $button) {

                $html .= '
                                        ' . $button->get();
            }

            $html .= '
                                    </div>';
            $html .= '
                                </div>';
        }

        if ($this->_filter) {

            $html .= '
                                <div class="col-5 col-md-2 col-lg-1">';
            $html .= '
                                    <div class="d-flex justify-content-start justify-content-md-end ' . ($this->_search ? 'pe-3' : '') . '">';

            $html .= '
                                        ' . Link::a(
                                            href: 'javascript:;',
                                            label: '<i class="fas fa-filter"></i>',
                                            attributes: [
                                                'title' => __tx('Filter'),
                                                'class' => 'btn btn-primary',
                                                'type' => 'button',
                                                'role' => 'button',
                                                'data-bs-toggle' => 'collapse',
                                                'data-bs-target' => '#filter_container',
                                                'aria-expanded' => 'false',
                                                'aria-controls' => 'filter_container',
                                            ],
                                        )->get();

            $html .= '
                                    </div>';
            $html .= '
                                </div>';
        }

        if ($this->_search) {

            $filter_prefix = self::_filter_prefix();
            $search_query = self::_search_query();
            $class_eraser = empty($search_query) ? 'd-none' : '';

            $html .= '
                                <div class="col-7 col-md-3">';
            $html .= '
                                    <form action="' . self::_search_uri() . '" method="post">';
            $html .= '
                                        <div class="form-group">';
            $html .= '
                                            <div class="input-group">';
            $html .= '
                                                <input type="text" id="' . $filter_prefix . 'search_query" name="' . $filter_prefix . 'search_query" value="' . $search_query . '" class="form-control search-query-input" placeholder="' . __tx('Search...') . '">';
            $html .= '
                                                <span class="input-group-text listview-search-eraser ' . $class_eraser . '" onclick="document.querySelector(`#' . $filter_prefix . 'search_query`).value = \'\'; this.classList.add(\'d-none\'); document.querySelector(`#' . $filter_prefix . 'search_query`).focus();"><i class="far fa-times"></i></span>';
            $html .= '
                                                <span class="input-group-text listview-search-clicker"><i class="far fa-search"></i></span>';
            $html .= '
                                            </div>';
            $html .= '
                                        </div>';
            $html .= '
                                    </form>';
            $html .= '
                                </div>';
        }

        $html .= '
                            </div>';
        $html .= '
                        </div>';
        $html .= '
                    </div>';
        $html .= '
                </div>';
        $html .= '
            </div>'; // .card-header

        if ($this->_filter || $this->_paginate) {

            $html .= '
            ' . $this->_get_filter_html();

            $html .= '
            <div class="card-header">';
            $html .= '
                <div class="container-fluid px-1">';
            $html .= '
                    <div class="row gx-1 justify-content-between">';
            $html .= '
                        <div class="d-flex justify-content-between">';
            $html .= '
                            <div class="d-flex align-items-center gap-1">';

            $html .= '
                                ' . $this->_get_applied_filters_html();

            $html .= '    
                            </div>';
            $html .= '
                            <div class="d-flex justify-content-end align-items-center gap-4">';

            if ($this->_share) {

                $html .= '
                                <a href="javascript:;" class="btn btn-light" onclick="copy2clipboard(\'' . rtrim(LUMIO_HOST, '/') . $this->_uri . '\');"><i class="fal fa-share-alt me-2"></i>' . __tx('Share') . '</a>';
            }

            $html .= '
                                <div class="d-flex justify-content-end align-items-center gap-4">';

            $html .= '
                                    ' . $this->_get_pagination_html();

            $html .= '    
                                </div>';
            $html .= '    
                            </div>';
            $html .= '
                        </div>';
            $html .= '
                    </div>';
            $html .= '
                </div>';
            $html .= '
            </div>'; // .card-header
        }

        $html .= '
            <div class="card-body p-0">';

        if (!empty($this->_data)) {

            $html .= '
                <div class="table-responsive">';
            $html .= '
                    <table class="table table-striped mb-0">';
            $html .= '
                        <thead>';
            $html .= '
                            <tr>';

            if ($this->_checkers) {

                $html .= '
                                <th class="ps-4 pe-2" style="width:40px"><input type="checkbox" class="check-all" /></th>';
            }

            if (!empty($this->_actions)) {

                $html .= '
                                <th class="ps-4 pe-2" style="width:40px">&nbsp;</th>';
            }

            foreach ($this->_columns as $column) {

                $col_width = $column->get_width();
                $col_width = !empty($col_width) ? ' style="width:' . htmlspecialchars($col_width) . 'px"' : '';

                $column_label = htmlspecialchars($column->get_label());

                $class_pe = 'pe-2';
                $class_sorting = '';
                if ($column->is_sortable()) {

                    $sort_column = $column->get_sort_column() ?? $column->get_name();
                    $sort_direction = Sorting::ASC;
                    if (self::$_request->get_sorting() && self::$_request->get_sorting()->get_column() == $sort_column) {
                        $sort_direction = self::$_request->get_sorting()->get_direction() == Sorting::ASC ? Sorting::DESC : Sorting::ASC;
                    }

                    $sort_link = self::_get_sorting_link(new Sorting($sort_column, $sort_direction));

                    $column_label = '<a href="' . $sort_link . '">' . $column_label . '</a>';

                    $class_pe = 'pe-4';
                    $class_sorting = 'th-sorting th-sorting-' . strtolower($sort_direction);
                }

                $html .= '
                                <th class="ps-4 ' . $class_pe . ' ' . $class_sorting . '" ' . $col_width . '>' . $column_label . '</th>';
            }

            $html .= '
                                <th class="w-auto">&nbsp;</th>';
            $html .= '
                            </tr>';
            $html .= '
                        </thead>';
            $html .= '
                    <tbody>';

            foreach ($this->_data as $row) {

                $html .= '
                            <tr>';

                if ($this->_checkers) {

                    $html .= '
                                <td class="ps-4 pe-2">';
                    $html .= '
                                    <input type="checkbox" 
                                            name="' . htmlspecialchars($this->_checker_name) . '[]" 
                                            value="' . htmlspecialchars($row[$this->_checker_key] ?? '') . '" />';
                    $html .= '
                                </td>';
                }

                if (!empty($this->_actions)) {

                    $html .= '
                                <td class="ps-4 pe-2">';

                    $html .= $this->_get_actions_html($row);

                    $html .= '
                                </td>';
                }

                foreach ($this->_columns as $column) {

                    $class_pe = 'pe-2';
                    if ($column->is_sortable()) {
                        $class_pe = 'pe-4';
                    }

                    $html .= '
                                <td class="ps-4 ' . $class_pe . '">' . $column->get($row) . '</td>';
                }

                $html .= '
                                <td class="w-auto">&nbsp;</td>';
                $html .= '
                            </tr>';
            }

            $html .= '
                        </tbody>';

            if (!empty($this->_summary_columns)) {

                $html .= '
                        <tfoot>';
                $html .= '
                            <tr>';

                if ($this->_checkers) {

                    $html .= '
                                <td class="ps-4 pe-2">&nbsp;</td>';
                }

                if (!empty($this->_actions)) {

                    $html .= '
                                <td class="ps-4 pe-2">&nbsp;</td>';
                }

                foreach ($this->_columns as $column) {

                    $name = $column->get_name();

                    $class_pe = 'pe-2';
                    if ($column->is_sortable()) {
                        $class_pe = 'pe-4';
                    }

                    if (in_array($name, $this->_summary_columns)) {
                        $sum = array_sum(array_column($this->_data, $name));
                        $html .= '
                                <td class="ps-4 ' . $class_pe . '"><strong>' . htmlspecialchars($sum) . '</strong></td>';
                    } else {
                        $html .= '
                                <td class="ps-4 ' . $class_pe . '"></td>';
                    }
                }

                $html .= '
                                <td class="w-auto">&nbsp;</td>';

                $html .= '
                            </tr>';
                $html .= '
                        </tfoot>';
            }

            $html .= '
                    </table>';

        } else {

            $html .= '
                    <div class="py-2 px-4">' . ($this->_empty_text ?? __tx('No data found.')) . '</div>';
        }

        $html .= '
                </div>'; // .table-responsive
        $html .= '
            </div>'; // .card-body



        $html .= '
            <div class="card-footer">';
        $html .= '
                <div class="container-fluid px-1">';
        $html .= '
                    <div class="row gx-1 justify-content-end">';
        $html .= '
                        <div class="col-auto">';

        if ($this->_paginate) {

            $html .= '
                            <div class="placeholder" id="listview_counts_info"><span class="opacity-0">Shown XX - XX out of XX</span></div>';
        }

        $html .= '
                        </div>';
        $html .= '
                    </div>';
        $html .= '
                </div>';
        $html .= '
            </div>'; // .card-footer



        $html .= '
        </div>'; // .card

        echo $html;
    }

    /**
     * Get the HTML for actions of the given row
     *
     * @author TB
     * @date 3.5.2025
     *
     * @param mixed $row
     *
     * @return string
     */
    private function _get_actions_html(mixed $row) : string {

        if (empty($this->_actions)) {
            return '';
        }

        $html = '
            <div class="dropdown">';
        $html .= '
                <a href="javascript:;" class="" data-bs-toggle="dropdown"><i class="far fa-ellipsis-h"></i></a>';
        $html .= '
                <div class="dropdown-menu">';

        foreach ($this->_actions as $action) {

            $link = $action->get_full_link($row);
            $label = $action->get_label();

            $icon = $action->get_icon();
            if (!empty($icon) && self::_contains_icon_class($icon)) {
                $icon = '<i class="' . $icon . ' me-2"></i>';
            } else {
                $icon = '';
            }

            if ($action->is_divider_before()) {

                $html .= '
                    <div><hr class="dropdown-divider"></div>';
            }

            if ($action->is_modal()) {

                $html .= '
                    ' . Link::modal(
                        link: $link,
                        label: $icon . $label,
                        params: ['class' => 'dropdown-item'],
                    )->get();

            } else {

                $html .= '
                    ' . Link::a(
                            href: $link,
                            label: $icon . $label,
                            attributes: ['class' => 'dropdown-item'],
                        )->get();
            }
        }

        $html .= '
                </div>';
        $html .= '
            </div>';

        return $html;
    }

    /**
     * Get HTML of the filter
     *
     * @author TB
     * @date 4.5.2025
     *
     * @return string
     */
    private function _get_filter_html() : string {

        if (!$this->_filter) {
            return '';
        }

        try {
            $dir = Config::get('app.filter.path_filters');
        } catch (\Throwable $e) {
            $dir = '';
        }

        $file = $dir . DIRECTORY_SEPARATOR . self::get_controller() . '_' . self::get_action() . '.filter.php';


        $html = '
        <div class="card-header bg-white py-4 collapse" id="filter_container">';
        $html .= '
            <div class="container-fluid px-1">';
        $html .= '
                <div class="row gx-1">';
        $html .= '
                    <div class="col-12">';

        if (file_exists($file)) {

            Form::filter(true); // enable the filter form

            ob_start();

            $form = Form::build();

            $form->row();

            include $file;

            $form->end_row();

            $form->hidden(new FormInput(
                name: '__fulluri',
                default: $this->_uri,
                force_default: true,
            ));

            $form->submit(new FormInput(
                label: __tx('Apply')
            ));

            // store the selected/filled filter form input values for later use in the applied filters badges
            $this->_applied_filters = $form->get_readable_values();

            $filter = ob_get_clean();
            $html .= $filter;

            Form::filter(false); // disable again, dont need that any further

        } else {

            $html .= '
                        <div class="alert alert-danger" role="alert">';
            $html .= '
                            <div class="d-flex gap-2 justify-content-start align-items-start">';
            $html .= '
                                <div class=""><i class="far fa-exclamation-triangle me-2"></i></div>';
            $html .= '
                                <div class="">';
            $html .= '
                                    ' . __tx('Filter file not found');
            $html .= ' 
                                    <div><code>' . htmlspecialchars($file) . '</code></div>';
            $html .= '
                                </div>';
            $html .= '
                            </div>';
            $html .= '
                        </div>';
        }

        $html .= '
                    </div>';
        $html .= '
                </div>';
        $html .= '
            </div>';
        $html .= '
        </div>';

        return $html;
    }

    private function _get_applied_filters_html() : string {

        if (!$this->_filter || empty($this->_applied_filters)) {
            return '';
        }

        try {
            $prefix = Config::get('app.filter.fields_prefix');
        } catch (\Throwable $e) {
            $prefix = '';
        }

        $html = '';
        foreach ($this->_applied_filters as $filter) {

            $name = $filter->get_name() ?? '';
            if (!empty($prefix)) {
                $name = str_replace($prefix, '', $name);
            }

            $html .= '
                                <span class="badge rounded-pill d-inline-flex align-items-center gap-1 text-bg-info">';
            $html .= '
                                    <small class="text-muted fw-normal">' . $filter->get_label() . '</small>';
            $html .= '
                                    <span>' . $filter->get_value() . '</span>';
            $html .= '
                                    <a href="javascript:;" class="text-body ms-1 filter-cancel" data-filter-name="' . $name . '">
                                        <i class="far fa-times"></i>
                                    </a>';
            $html .= '
                                </span>';
        }

        return $html;
    }

    private function _get_pagination_html() : string {

        if (!$this->_paginate) {
            return '';
        }

        $html = '
            <ul class="pagination mb-0" id="listview_pagination">';
        $html .= '
                <li class="page-item">
                    <a class="page-link placeholder-bg border-transparent" tabindex="-1" href="javascript:;">
                        ' . __tx('Loading pagination...') . '
                    </a>
                </li>';
        $html .= '
            </ul>';

        return $html;
    }

}
