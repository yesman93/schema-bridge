<?php

namespace Lumio\View\Components;

use Lumio\Config;
use Lumio\DTO\Model\Sorting;
use Lumio\IO\URIParamsParser;
use Lumio\Log\Logger;
use Lumio\View\Component;
use Lumio\DTO\Log\LogRecord;
use Lumio\Traits;

class LogViewer extends Component {

    use Traits\Log\LogLevel;
    use Traits\Log\LogFile;

    /**
     * Title of the log view
     *
     * @var string
     */
    private string $_logview_title = '';

    /**
     * Log channels selection
     *
     * @var array
     */
    private array $_channels = [];

    /**
     * Current channel
     *
     * @var string
     */
    private string $_channel = '';

    /**
     * Log records
     *
     * @var array
     */
    private array $_data = [];

    /**
     * Total of the current dataset
     *
     * @var int
     */
    private int $_total = 0;

    /**
     * Current full URI including page number and applied filters
     *
     * @var string
     */
    private string $_uri = '';

    /**
     * Set title
     *
     * @param string $title
     *
     * @return self
     */
    public function title(string $title): self {
        $this->_logview_title = $title;
        return $this;
    }

    /**
     * Set channels
     *
     * @param array $channels
     *
     * @return self
     */
    public function channels(array $channels): self {
        $this->_channels = $channels;
        return $this;
    }

    /**
     * Set current channel
     *
     * @param string $channel
     *
     * @return self
     */
    public function channel(string $channel): self {
        $this->_channel = $channel;
        return $this;
    }

    /**
     * Set log records
     *
     * @param array $data
     *
     * @return self
     */
    public function data(array $data): self {
        $this->_data = $this->_apply_data_control($data);
        return $this;
    }

    /**
     * Set the URI
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
     * Build a new instance of log viewer
     *
     * @param array|null $data
     *
     * @return self
     */
    public static function build(?array $data = null) : self {

        $instance = new self();

        $instance->title(self::get_title() ?? '');

        if ($data !== null) {
            $instance->data($data);
        }

        return $instance;
    }

    /**
     * Get URI with given channel in filters
     *
     * @param string $channel
     *
     * @return string
     */
    private static function _channel_uri(string $channel) : string {

        $filter_data = self::$_request->filter();
        if (empty($filter_data)) {
            $filter_data = [];
        }

        $filter_data['channel'] = $channel;

        unset($filter_data['log_file']);

        return URIParamsParser::build(
            controller: self::$_controller,
            action: self::$_action,
            request: self::$_request,
            realm: self::$_realm,
            params: self::$_params,
            filter_data: $filter_data,
        );
    }

    /**
     * Get URI with given file in filters
     *
     * @param string $filename
     *
     * @return string
     */
    private static function _file_uri(string $filename) : string {

        $filter_data = self::$_request->filter();
        if (empty($filter_data)) {
            $filter_data = [];
        }

        $filter_data['log_file'] = $filename;

        return URIParamsParser::build(
            controller: self::$_controller,
            action: self::$_action,
            request: self::$_request,
            realm: self::$_realm,
            params: self::$_params,
            filter_data: $filter_data,
        );
    }

    /**
     * Get URI with given group by request ID in filters
     *
     * @param bool $group_by_requestid
     *
     * @return string
     */
    private static function _group_by_requestid_uri(bool $group_by_requestid) : string {

        $filter_data = self::$_request->filter();
        if (empty($filter_data)) {
            $filter_data = [];
        }

        $filter_data['group_by_requestid'] = $group_by_requestid ? 1 : 0;

        return URIParamsParser::build(
            controller: self::$_controller,
            action: self::$_action,
            request: self::$_request,
            realm: self::$_realm,
            params: self::$_params,
            filter_data: $filter_data,
        );
    }

    /**
     * Get URI with given request ID in filters
     *
     * @param string $request_id
     *
     * @return string
     */
    private static function _request_id_uri(string $request_id) : string {

        $filter_data = self::$_request->filter();
        if (empty($filter_data)) {
            $filter_data = [];
        }

        $filter_data['search_query'] = $request_id;

        return URIParamsParser::build(
            controller: self::$_controller,
            action: self::$_action,
            request: self::$_request,
            realm: self::$_realm,
            params: self::$_params,
            filter_data: $filter_data,
        );
    }

    /**
     * Render the log view
     *
     * @return void
     */
    public function render() : void {

        $html = '';

        $html .= '
        <div class="container-fluid log-viewer">';
        $html .= '
            <div class="row">';
        $html .= '
                <div class="col-3">';
        $html .= '
                    <div class="">';
        $html .= '
                        <h3 class="">' . $this->_logview_title . '</h3>';
        $html .= '
                    </div>';
        $html .= '
                <div class="mt-4">';
        $html .= '
                    <div class="">';
        $html .= '
                    <label class="form-label mb-1" for="channel">' . __tx('Channel') . '</label>';
        $html .= '
                        <select name="channel" id="channel" class="form-select">';

        foreach ($this->_channels as $channel) {

            $value = self::_channel_uri($channel);

            $html .= '
                            <option value="' . $value . '" ' . ($this->_channel === $channel ? 'selected' : '') . '>' . $channel . '</option>';
        }

        $html .= '
                        </select>';
        $html .= '
                        </div>';
        $html .= '
                    </div>';
        $html .= '
                    <div class="mt-5">';

        $files = self::get_channel_files($this->_channel, true);

        $current_file = self::$_request->filter('log_file');
        if (empty($current_file)) {
            $first_file = reset($files);
            if (!empty($first_file)) {
                $current_file = $first_file['filename'] ?? '';
            }
        }

        if (!empty($files)) foreach ($files as $file) {

            $filename = $file['filename'] ?? '';
            $value = self::_file_uri($filename);

            $checked = $current_file == $filename ? self::_not_empty_to_attr('checked', 'checked') : '';

            $html .= '
                        <div class="form-check card-radio">';
            $html .= '
                            <input id="file_' . $filename . '" name="log_file" value="' . $value . '" type="radio" class="form-check-input" ' . $checked . ' />';
            $html .= '
                            <label class="form-check-label py-2 pe-4" for="file_' . $filename . '">';
            $html .= '
                                <div class="d-flex justify-content-between align-items-center fs-14 me-2">
                                    <div class="">
                                        <div class="">
                                        <i class="fas fa-file me-1"></i>
                                        ' . $filename . '
                                        </div>
                                    </div>
                                    <div class="text-muted opacity-50">
                                        ' . $file['size_readable'] . '
                                    </div>
                                </div>';
            $html .= '
                            </label>';
            $html .= '
                        </div>';

        } else {

            $html .= '
                        <div class="alert alert-info" role="alert">';
            $html .= '
                            <div class="d-flex align-items-center">';
            $html .= '
                                <div class="flex-shrink-0">';
            $html .= '
                                    <i class="fas fa-info-circle fa-lg"></i>';
            $html .= '
                                </div>';
            $html .= '
                                <div class="flex-grow-1 ms-2">';
            $html .= '
                                    <h6 class="alert-heading mb-0">' . __tx('No log files found') . '</h6>';
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
                <div class="col-9">';
        $html .= '
                    <div class="container-fluid">';
        $html .= '
                        <div class="row justify-content-between align-items-start">';
        $html .= '
                            <div class="col-5">';
        $html .= '
                                <div class="d-flex flex-wrap gap-2">';

        $filter_levels = array_flip(self::$_request->filter('levels') ?? []);
        $is_filter_levels = self::$_request->has_filter('levels');
        $levels = self::get_level_options();
        if (!empty($levels)) foreach ($levels as $level) {

            if ($is_filter_levels) {
                $checked = array_key_exists($level['value'], $filter_levels) ? self::_not_empty_to_attr('checked', 'checked') : '';
            } else {
                $checked = self::_not_empty_to_attr('checked', 'checked');
            }

            $color_class = self::get_level_color($level['value'] ?? null);
            $description = $level['label'] ?? '';

            $html .= '
                                    <label class="badge text-bg-' . $color_class . ' d-inline-flex align-items-center gap-1 py-1 px-2" for="levels_' . $level['value'] . '">';
            $html .= '
                                        <input type="checkbox" id="levels_' . $level['value'] . '" name="levels[]" value="' . $level['value'] . '" ' . $checked . ' />';
            $html .= '
                                            <span class="fw-normal text-uppercase opacity-75">';
            $html .= '
                                                ' . $description . ':';
            $html .= '    
                                            </span>';
            $html .= '
                                            <span>';
            $html .= '
                                                654';
            $html .= '
                                            </span>';
            $html .= '
                                        </label>';
        }

        $html .= '
                                </div>';
        $html .= '
                            </div>';
        $html .= '
                            <div class="col-7">';

        $filter_prefix = self::_filter_prefix();
        $search_query = self::_search_query();
        $class_eraser = empty($search_query) ? 'd-none' : '';

        $html .= '
                                <form action="' . self::_search_uri() . '" method="post">';
        $html .= '
                                    <input type="hidden" id="__fulluri" name="__fulluri" value="' . self::_get_action_uri() . '" />';
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

        $filter_data = self::$_request->filter();
        $group_by_requestid = isset($filter_data['group_by_requestid']) ? $filter_data['group_by_requestid'] : false;

        $uri_groupby_requestid_on = self::_not_empty_to_attr('data-uri-on', self::_group_by_requestid_uri(true));
        $uri_groupby_requestid_off = self::_not_empty_to_attr('data-uri-off', self::_group_by_requestid_uri(false));

        $html .= '
                                <div class="mt-3 d-flex justify-content-between align-items-center">';
        $html .= '
                                    <div class="">';
        $html .= '
                                        <div class="form-check">';
        $html .= '
                                            <input type="checkbox" class="form-check-input" name="group_by_requestid" id="group_by_requestid" value="1" ' . ($group_by_requestid ? self::_not_empty_to_attr('checked', 'checked') : '') . ' ' . $uri_groupby_requestid_on . ' ' . $uri_groupby_requestid_off . ' />';
        $html .= '
                                            <label class="form-check-label fs-12 text-muted" for="group_by_requestid">' . __tx('Group by request ID') . '</label>';
        $html .= '
                                        </div>';
        $html .= '
                                    </div>';
        $html .= '
                                    <div class="d-flex gap-3 align-items-center">';
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
                    <div class="mt-4">';
        $html .= '
                        <div class="container-fluid">';
        $html .= '
                            <div class="row">';
        $html .= '
                                <div class="col-12">';

//        $data = $this->_apply_data_control($this->_data);
        $data = $this->_data;
        if (!empty($data)) {

            $class_headers = 'fs-12 fw-bold text-secondary opacity-75';

            $html .= '
                                    <div class="d-flex justify-content-start align-items-start gap-3 px-3 pb-2">';

            $html .= '
                                        <div class="' . $class_headers . ' log-col-level">' . __tx('Level') . '</div>';
            $html .= '
                                        <div class="' . $class_headers . ' log-col-datetime">' . __tx('Date and time') . '</div>';
            $html .= '
                                        <div class="' . $class_headers . ' log-col-environment">' . __tx('Environment') . '</div>';
            $html .= '
                                        <div class="' . $class_headers . ' log-col-requestid">' . __tx('Request ID') . '</div>';
            $html .= '
                                        <div class="' . $class_headers . ' log-col-message">' . __tx('Content') . '</div>';

            $html .= '
                                    </div>';

            $request_id = '';
            foreach ($data as $row) {

                $datetime = date('j.n.Y G:i:s', strtotime($row->datetime()));
                $level_description = self::get_level_description($row->level());
                $level_color = self::get_level_color($row->level());
                $level_icon = self::_get_icon_html_by_level($row->level());

                $filter_request = '';
                if (!empty($row->request_id())) {

                    $filter_request = '
                        <a href="' . self::_request_id_uri($row->request_id()) . '" class="me-1">
                            <i class="fal fa-filter"></i></a>';
                }

                $context = $row->context();

                $context_link = '';
                $context_link_key = '';
                if (!empty($context)) foreach ($context as $ck => $cv) {

                    if (in_array($ck, ['link', 'url', 'uri']) && strpos($cv, '/') !== false) {
                        $context_link = ' <a href="' . $cv . '" target="_blank">' . $cv . '</a>';
                        $context_link_key = $ck;
                    }
                }

                if (!empty($context_link)) {
                    unset($context[$context_link_key]);
                }

                $row_separation = 'mt-0';
                if ($group_by_requestid && (!empty($request_id) && $request_id != $row->request_id())) {
                    $row_separation = 'mt-2';
                }


                $html .= '
                                    <div class="card ' . $row_separation . ' border-bottom log-row">';
                $html .= '
                                        <div class="card-body py-1">';
                $html .= '
                                            <div class="d-flex justify-content-start align-items-start gap-3">';

                $html .= '
                                                <div class="log-col-level fs-14 text-' . $level_color . '">' . $level_icon . '<span class="fs-12 fw-bold">' . $level_description . '</span></div>';
                $html .= '
                                                <div class="log-col-datetime text-secondary fs-12">' . $datetime . '</div>';
                $html .= '
                                                <div class="log-col-environment text-secondary fs-12">' . $row->env() . '</div>';
                $html .= '
                                                <div class="log-col-requestid text-secondary fs-12">' . $filter_request . $row->request_id() . '</div>';
                $html .= '
                                                <div class="log-col-message fs-14">' . $row->message() . $context_link . '</div>';
//                $html .= '
//                                                <div class="log-col-context">' . $row->context() . '</div>';

                $html .= '
                                            </div>';

                if (!empty($context)) {

                    $html .= '
                                            <div class="d-flex justify-content-start align-items-start gap-3 pb-2">';
                    $html .= '
                                                <div class="border rounded mt-2 p-2 bg-light w-100 fs-14">';
                    $html .= '
                                                    <code><pre class="mb-0">' . print_r($context, true) . '</pre></code>';
                    $html .= '
                                                </div>';
                    $html .= '
                                            </div>';
                }

                $html .= '
                                        </div>';
                $html .= '
                                    </div>';


                $request_id = $row->request_id();
            }

        } else {

            $html .= '
            <div class="alert alert-info" role="alert">';
            $html .= '
                <div class="d-flex align-items-center">';
            $html .= '
                    <div class="flex-shrink-0">';
            $html .= '
                        <i class="fas fa-info-circle fa-lg"></i>';
            $html .= '
                    </div>';
            $html .= '
                    <div class="flex-grow-1 ms-2">';
            $html .= '
                        <h6 class="alert-heading mb-0">' . __tx('No log records found') . '</h6>';
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
        $html .= '
                </div>';
        $html .= '
            </div>';
        $html .= '
        </div>';


        echo $html;
    }

    /**
     * Apply data control mechanisms - pagination, filters and sorting
     *
     * @param array $records
     *
     * @return array
     */
    private function _apply_data_control(array $records): array {

        try {
            $per_page = Config::get('app.pagination.per_page');
        } catch (\Throwable $e) {
            $per_page = 50;
        }

        $page = self::$_request->get_page();
        $search = self::_search_query();

        // Filter
        if (!empty($search)) {

            $records = array_filter($records, function ($record) use ($search) {
                return str_contains(strtolower($record->message()), strtolower($search)) ||
                    str_contains(strtolower($record->request_id()), strtolower($search));
            });
        }

        // Levels
        if (!empty($levels = self::$_request->filter('levels'))) {

            $levels = array_flip($levels);
            $records = array_filter($records, function ($record) use ($levels) {
                return array_key_exists(strtolower($record->level()), $levels);
            });
        }

        $this->_total = empty($records) ? 0 : count($records);

        // Pagination
        $offset = max(0, ($page - 1) * $per_page);
        $ret = array_slice($records, $offset, $per_page);

        return $ret;
    }

    /**
     * Get pagination HTML
     *
     * @return string
     */
    private function _get_pagination_html() : string {

        try {
            $per_page = Config::get('app.pagination.per_page');
        } catch (\Throwable $e) {
            $per_page = 50;
        }

        $filter_data = self::$_request->filter();

        return Pagination::build(
            page: self::$_request->get_page(),
            per_page: $per_page,
            total: $this->_total,
            base_uri: self::_get_base_uri(),
            filters: URIParamsParser::build_filter($filter_data),
            sorting: URIParamsParser::build_sorting(self::$_request->get_sorting()),
            params: self::$_params ?? [],
        )->get();
    }

    /**
     * Get icon HTML by given level
     *
     * @param string $level
     *
     * @return string
     */
    private static function _get_icon_html_by_level(string $level) : string {

        if (empty($level)) {
            return '';
        }

        $icon = self::_get_icon_by_level($level);
        if (empty($icon)) {
            return '';
        }

        return '<i class="fad ' . $icon . ' me-1"></i>';
    }

    /**
     * Get icon class by given level
     *
     * @param string $level
     *
     * @return string
     */
    private static function _get_icon_by_level(string $level) : string {

        if (empty($level)) {
            return '';
        }

        return match (strtolower($level)) {
            self::_LEVEL_EMERGENCY => 'fa-exclamation-triangle',
            self::_LEVEL_ALERT => 'fa-exclamation-circle',
            self::_LEVEL_CRITICAL => 'fa-exclamation-triangle',
            self::_LEVEL_ERROR => 'fa-exclamation-circle',
            self::_LEVEL_WARNING => 'fa-exclamation-circle',
            self::_LEVEL_NOTICE => 'fa-info-circle',
            self::_LEVEL_INFO => 'fa-info-circle',
            self::_LEVEL_DEBUG  => 'fa-info-circle',
            default => '',
        };
    }

}
