<?php

namespace Lumio\View;

use Lumio\Config;
use Lumio\DTO\Model\Pagination;
use Lumio\DTO\View\Breadcrumb;
use Lumio\Exceptions\LumioViewException;
use Lumio\IO\Flash;
use Lumio\IO\Request;
use Lumio\Traits;

class View {

    use Traits\View\Master;
    use Traits\View\SEO {
        set_title as _set_title;
    }
    use Traits\View\OpenGraph;

    /**
     * Variables accessible in the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    protected array $_vars = [];

    /**
     * Rendered template content
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var string
     */
    protected string $_template_content = '';

    /**
     * Master page
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var string
     */
    protected string $_master_page = '';

    /**
     * Realm name
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var string
     */
    protected string $_realm = '';

    /**
     * Controller name
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var string
     */
    protected string $_controller = '';

    /**
     * Action name
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var string
     */
    protected string $_action = '';

    /**
     * Request object
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var Request|null
     */
    protected ?Request $_request = null;

    /**
     * Additional params from URI
     *
     * @author TB
     * @date 6.5.2025
     *
     * @var array|null
     */
    protected ?array $_params = null;

    /**
     * CSS files to include
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    protected array $_css_files = [];

    /**
     * JS files to include
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    protected array $_js_files = [];

    /**
     * Pagination
     *
     * @author TB
     * @date 1.5.2025
     *
     * @var Pagination
     */
    protected Pagination $_pagination;

    /**
     * CSRF token
     *
     * @author TB
     * @date 19.5.2025
     *
     * @var string
     */
    protected string $_csrf_token = '';

    /**
     * CSRF token form field name
     *
     * @author TB
     * @date 19.5.2025
     *
     * @var string
     */
    protected string $_csrf_field = '';

    /**
     * View - rendering engine
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string|null $controller
     * @param string|null $action
     * @param Request|null $request
     *
     * @return void
     */
    public function __construct(?string $controller = null, ?string $action = null, ?Request $request = null) {

        if ($controller !== null) {
            $this->_controller = $controller;
        }

        if ($action !== null) {
            $this->_action = $action;
        }

        if ($request !== null) {
            $this->_request = $request;
        }
    }

    /**
     * Set the title of the view
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param string $title
     *
     * @return void
     */
    public function set_title(string $title) : void {
        $this->_set_title($title);
        Component::set_title($title);
    }

    /**
     * Set the pagination
     *
     * @author TB
     * @date 1.5.2025
     *
     * @param Pagination $pagination
     *
     * @return void
     */
    public function set_pagination(Pagination $pagination): void {
        $this->_pagination = $pagination;
        Component::set_pagination($pagination);
    }

    /**
     * Assign a value to the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function assign(string $key, mixed $value): void {
        $this->_vars[$key] = $value;
    }

    /**
     * Set the realm
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $realm
     *
     * @return void
     */
    public function set_realm(string $realm): void {
        $this->_realm = $realm;
        Component::set_realm($realm);
    }

    /**
     * Set the controller
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $controller
     *
     * @return void
     */
    public function set_controller(string $controller): void {
        $this->_controller = $controller;
    }

    /**
     * Set the action
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $action
     *
     * @return void
     */
    public function set_action(string $action): void {
        $this->_action = $action;
    }

    /**
     * Set URI params
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param array $params
     *
     * @return void
     */
    public function set_params(array $params) : void {
        $this->_params = $params;
        Component::set_params($params);
    }

    /**
     * Set the master page
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $master_page
     *
     * @return void
     */
    public function master(string $master_page): void {

        try {
            $file = Config::get('app.view.path_master_pages') . DIRECTORY_SEPARATOR . ucfirst($master_page) . '.Master.php';
        } catch (\Throwable $e) {
            $file = '';
            lumio_fail($e->getMessage(), $e->getCode());
        }

        if (!empty($file) && !file_exists($file)) {
            throw new LumioViewException("Master page not found: " . $file);
        }

        $this->_master_page = $master_page;
        Component::set_master($master_page);
    }

    /**
     * Add a CSS file to the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $path
     *
     * @return void
     */
    public function css(string $path): void {

        $assets_path = Config::get('app.view.path_assets');
        if (!file_exists($assets_path)) {
            throw new LumioViewException("Assets path not found: " . $assets_path);
        }

        $filepath = $assets_path . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $path . '.css';
        if (file_exists($filepath)) {
            $this->_css_files[] = $path;
        }
    }

    /**
     * Add a JS file to the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $path
     *
     * @return void
     */
    public function js(string $path): void {

        $assets_path = Config::get('app.view.path_assets');
        if (!file_exists($assets_path)) {
            throw new LumioViewException("Assets path not found: " . $assets_path);
        }

        $filepath = $assets_path . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $path . '.js';
        if (file_exists($filepath)) {
            $this->_js_files[] = $path;
        }
    }

    /**
     * Get a variable from the view - enables direct access to view variables property style
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key): mixed {
        return $this->_vars[$key] ?? null;
    }

    /**
     * Render the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return void
     *
     * @throws \Exception
     */
    public function render(): void {

        if (empty($this->title(false))) {
            $this->set_title(ucfirst($this->_action));
        }

        // Load common assets from config
        $this->_load_common_assets();

        // Support for realms
        $realm_path = empty($this->_realm) ? '' : $this->_realm . DIRECTORY_SEPARATOR;

        $template_path = Config::get('app.view.path_pages') . DIRECTORY_SEPARATOR . $realm_path . $this->_controller . DIRECTORY_SEPARATOR . $this->_action . '.php';

        if (!file_exists($template_path)) {
            throw new LumioViewException("Template not found: " . $template_path);
        }

        ob_start();
        include $template_path;
        $this->_template_content = ob_get_clean();

        $master_path = Config::get('app.view.path_master_pages') . DIRECTORY_SEPARATOR . ucfirst($this->_master_page) . '.Master.php';

        if (!file_exists($master_path)) {
            throw new LumioViewException("Master page not found: " . $master_path);
        }

        ob_start();
        include $master_path;
        $final_output = ob_get_clean();

        if (Config::get('app.view.minify_html')) {
            $final_output = $this->_minify_html($final_output);
        }

        echo $final_output;
    }

    /**
     * Get content of the rendered template
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return string
     */
    public function content(): string {
        return $this->_template_content;
    }

    /**
     * Minify HTML content
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $html
     *
     * @return string
     */
    private function _minify_html(string $html): string {

        $search = [
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s'
        ];

        $replace = ['>', '<', '\\1'];

        return preg_replace($search, $replace, $html);
    }

    /**
     * Include a partial view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $partial_name
     *
     * @return void
     *
     * @throws \Exception
     */
    public function partial(string $partial_name): void {

        $path = Config::get('app.view.path_partials') . DIRECTORY_SEPARATOR . $partial_name . '.php';

        if (!file_exists($path)) {
            throw new LumioViewException("Partial not found: " . $path);
        }

        include $path;
    }

    /**
     * Load common assets from config
     *
     * @author TB
     * @date 29.4.2025
     *
     * @return void
     *
     * @throws LumioViewException
     */
    private function _load_common_assets(): void {

        $path_assets = Config::get('app.view.path_assets');

        if (!file_exists($path_assets)) {
            throw new LumioViewException("Assets path not found: " . $path_assets);
        }

        $assets_css = Config::get('app.view.assets.css');
        if (!empty($assets_css)) foreach ($assets_css as $asset) {
            $this->css($asset);
        }

        $assets_js = Config::get('app.view.assets.js');
        if (!empty($assets_js)) foreach ($assets_js as $asset) {
            $this->js($asset);
        }

        if ($this->_master_page !== self::MASTER_EMPTY) {

            $assets_master_css = Config::get('app.view.assets.' . ucfirst($this->_master_page) . '.css');
            if (!empty($assets_master_css)) foreach ($assets_master_css as $asset) {
                $this->css($asset);
            }

            $assets_master_js = Config::get('app.view.assets.' . ucfirst($this->_master_page) . '.js');
            if (!empty($assets_master_js)) foreach ($assets_master_js as $asset) {
                $this->js($asset);
            }
        }
    }

    /**
     * Include CSS files in the view
     *
     * @author TB
     * @date 29.4.2025
     *
     * @return void
     *
     * @throws \Exception
     */
    public function include_css(): void {

        $path_assets_public = Config::get('app.view.path_assets_public');

        foreach ($this->_css_files as $file) {
            $filepath = $path_assets_public . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file . '.css?v=' . CACHE_VERSION;
            $filepath = htmlspecialchars($filepath);
            $filepath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filepath);
            echo '<link rel="stylesheet" href="' . $filepath . '">' . PHP_EOL;
        }
    }

    /**
     * Include JS files in the view
     *
     * @author TB
     * @date 29.4.2025
     *
     * @return void
     *
     * @throws \Exception
     */
    public function include_js(): void {

        $path_assets_public = Config::get('app.view.path_assets_public');

        foreach ($this->_js_files as $file) {
            $filepath = $path_assets_public . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $file . '.js?v=' . CACHE_VERSION;
            $filepath = htmlspecialchars($filepath);
            $filepath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filepath);
            echo '<script src="' . $filepath . '"></script>' . PHP_EOL;
        }
    }

    /**
     * Render and run flash messages
     *
     * @author TB
     * @date 13.5.2025
     *
     * @return void
     */
    public function flash_messages(): void {

        $html = '';

        foreach ([
            'errors' => 'danger',
            'warnings' => 'warning',
            'infos' => 'info',
            'successes' => 'success',
         ] as $property => $type) {

            $messages = Flash::{'get_' . $property}();

            if (!empty($messages)) foreach ($messages as $msg) {

                $html .= '
                <div class="toast flash-message-toast align-items-center text-bg-' . $type . ' border-0 fade mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000" data-bs-autohide="true">';
                $html .= '
                    <div class="d-flex justify-content-between align-items-start">';
                $html .= '
                        <div class="toast-body">';

                $html .= '
                            ' . htmlspecialchars($msg);

                $html .= '
                        </div>';
                $html .= '
                        <div class="toast-body py-2">';
                $html .= '
                            <button type="button" class="btn-close text-bg-' . $type . '" data-bs-dismiss="toast" aria-label="' . __tx('Close') . '"></button>';
                $html .= '
                        </div>';
                $html .= '
                    </div>';
                $html .= '
                </div>';
            }
        }

        if (!empty($html)) {

            $html = '
            <div class="toast-container position-fixed top-0 start-50 p-2" style="z-index: 1100; transform: translateX(-50%)">
                ' . $html . '
            </div>';

            $html .= '
            <script type="text/javascript">
            
                document.addEventListener(\'DOMContentLoaded\', function () {
            
                    var toastElList = [].slice.call(document.querySelectorAll(\'.flash-message-toast\'));
                    toastElList.forEach(function (toastEl) {
                        var toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    });
                });
            
            </script>';
        }

        echo $html;
    }

    /**
     * Set given CSRF token and form field name
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param string $token
     * @param string $field
     *
     * @return void
     */
    public function set_csrf(string $token, string $field): void {

        $this->_csrf_token = $token;
        $this->_csrf_field = $field;

        Component::set_csrf_token($token);
        Component::set_csrf_field($field);
    }

    /**
     * Set breadcrumbs to view components
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param Breadcrumb $breadcrumb
     *
     * @return void
     */
    public function set_breadcrumb(Breadcrumb $breadcrumb): void {
        Component::set_breadcrumb($breadcrumb);
    }

    /**
     * Render CSRF token in meta tag
     *
     * @author TB
     * @date 24.5.2025
     *
     * @return void
     */
    public function meta_csrf(): void {
        echo '<meta name="' . $this->_csrf_field . '" content="' . $this->_csrf_token . '" />';
    }

}
