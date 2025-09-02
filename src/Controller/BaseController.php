<?php

namespace Lumio\Controller;

use Exception;
use Lumio\Auth\Logged;
use Lumio\Container;
use Lumio\DTO\File\UploadedFile;
use Lumio\DTO\IO\FileResponse;
use Lumio\DTO\IO\JsonResponse;
use Lumio\DTO\IO\RedirectResponse;
use Lumio\DTO\IO\XmlResponse;
use Lumio\DTO\Model\Pagination;
use Lumio\DTO\View\BreadcrumbItem;
use Lumio\Exceptions\LumioControllerException;
use Lumio\Exceptions\LumioPaginationTotalException;
use Lumio\Exceptions\LumioViewException;
use Lumio\Exceptions\Messages\LumioErrorException;
use Lumio\Exceptions\Messages\LumioInfoException;
use Lumio\Exceptions\Messages\LumioSuccessException;
use Lumio\Exceptions\Messages\LumioWarningException;
use Lumio\IO\Flash;
use Lumio\IO\MessageBag;
use Lumio\IO\Request;
use Lumio\IO\RequestValidator;
use Lumio\IO\Response;
use Lumio\Log\DatabaseLogger;
use Lumio\Log\DatabaseLoggerProxy;
use Lumio\Log\Logger;
use Lumio\Model\BaseModel;
use Lumio\Routing\History;
use Lumio\Routing\RouteResolver;
use Lumio\Security\CSRF;
use Lumio\View\Helpers\BreadcrumbBuilder;
use Lumio\View\View;

class BaseController {

    /**
     * instance of the MVC model
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var BaseModel|null
     */
    protected ?BaseModel $_model;

    /**
     * request object with data
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var Request
     */
    protected Request $_request;

    /**
     * response object
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var Response
     */
    protected Response $_response;

    /**
     * view object
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var View
     */
    protected View $_view;

    /**
     * instance of the container
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var Container|null
     */
    private ?Container $_container = null;

    /**
     * Current action method
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    protected string $_action = '';

    /**
     * Page title
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    protected string $_title = '';

    /**
     * Name of the realm
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var string
     */
    protected string $_realm = '';

    /**
     * Name of the controller
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    protected string $_name = '';

    /**
     * Name of the controller in plural
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    protected string $_name_plural = '';

    /**
     * Render flag
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var bool
     */
    private bool $_is_render = true;

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
     * Instance of database logger
     *
     * @author TB
     * @date 25.5.2025
     *
     * @var DatabaseLogger|null
     */
    private ?DatabaseLogger $_logger = null;

    /**
     * base controller - parent to all controllers
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param BaseModel|null $model
     * @param Request $request
     * @param Response $response
     * @param View $view
     *
     * @return void
     */
    public function __construct(?BaseModel $model, Request $request, Response $response, View $view) {

        $this->_model = $model;
        $this->_request = $request;
        $this->_response = $response;
        $this->_view = $view;

        $this->_set_input_data2model();
        $this->_init_view();
    }

    /**
     * Initialize the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return void
     */
    private function _init_view() : void {

        if (Logged::is_logged()) {
            $this->master(View::MASTER_PRIVATE);
        } else {
            $this->master(View::MASTER_PUBLIC);
        }
    }

    /**
     * Set input data to model
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return void
     */
    private function _set_input_data2model() {

        $data = array_merge(
            $this->_request->get(),
            $this->_request->post(),
            $this->_request->request()
        );

        if ($this->_model !== null && method_exists($this->_model, 'set_data')) {

            $this->_model->set_data($data);

            $this->_model->page($this->_request->get_page());

            $this->_model->sorting($this->_request->get_sorting());

            $this->_model->set_filter_data($this->_request->filter());
        }
    }

    /**
     * Set a container instance
     *
     * @author TB
     * @date 8.5.2025
     *
     * @param Container|null $container
     *
     * @return void
     */
    public function set_container(?Container $container) : void {

        $this->_container = $container;

        // TODO: setter for model - to allow it to use the logger too
    }

    /**
     * Get the container instance
     *
     * @author TB
     * @date 8.5.2025
     *
     * @return Container|null
     */
    protected function container() : ?Container {
        return $this->_container;
    }

    /**
     * Process and call the action method
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $action
     * @param array $params
     *
     * @return mixed
     */
    public function process(string $action = 'index', array $params = []): mixed {

        $this->set_action($action);

        try {

            $this->_before();

            $result = call_user_func_array([$this, $action], $params) ?? null;

            $this->_after();

            return $result;

        } catch (LumioControllerException $e) {

            Logger::channel('app')->emergency($e->getMessage());

            $this->_response->status($e->getCode())->body($e->getMessage())->fail();

        } catch (LumioPaginationTotalException $e) { // catch pagination total of records

            ob_clean();
            $total = $e->get_total();
            $this->_response->status(Response::HTTP_200)->body($total)->send();

        } catch (LumioSuccessException $e) { // catch and add success message

            $message = $e->get_message();
            $name = $e->get_name();

            MessageBag::success($message, $name);

        } catch (LumioInfoException $e) { // catch and add info message

            $message = $e->get_message();
            $name = $e->get_name();

            MessageBag::info($message, $name);

        } catch (LumioWarningException $e) { // catch and add warning message

            $message = $e->get_message();
            $name = $e->get_name();

            MessageBag::warning($message, $name);

        } catch (LumioErrorException $e) { // catch and add error message

            $message = $e->get_message();
            $name = $e->get_name();

            MessageBag::error($message, $name);

        } catch (LumioViewException $e) {

            $this->response()->status(Response::HTTP_404)->body($e->getMessage())->fail();

        } catch (\PDOException $e) {

            $this->assign('error_message', $e->getMessage());
            $this->assign('error_code', $e->getCode());

            $this->_view->set_realm('lumio');
            $this->_view->set_controller('error');
            $this->set_action('database');
            $this->title(__tx('Database error'));

            try {

                Logger::channel('db')->emergency($e->getMessage());

                $this->render();
                $this->set_render(false);

            } catch (\Throwable $e) {
                $this->response()->status($e->getCode())->body($e->getMessage())->fail();
            }

            // TODO: return as ErrorResponse and handle the rendering outside

            return null;

        } catch (Exception $e) {

            $this->response()->status($e->getCode())->body($e->getMessage())->fail();

        } finally {
            //return null;
        }

    }

    /**
     * Set action method
     *
     * @author TB
     * @date 30.4.2025
     *
     * @param string $action
     *
     * @return void
     */
    protected function set_action($action) : void {
        $this->_action = $action;
        $this->_view->set_action($action);
    }

    /**
     * Check if a form was submitted in current action
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string|null $name Custom submit name (can be multiple submits in one action)
     *
     * @return bool
     */
    protected function is_submit(?string $name = null): bool {

        $submit_value = $this->_request->get_submit($name ?? $this->_action);

        return !empty($submit_value);
    }

    /**
     * Set page title
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $title
     *
     * @return void
     */
    public function title(string $title): void {
        $this->_title = $title;
        $this->_view->set_title($title);
    }

    /**
     * Set SEO description
     *
     * @author TB
     * @date 30.4.2025
     *
     * @param string $description
     *
     * @return void
     */
    public function description(string $description): void {
        $this->_view->set_description($description);
    }

    /**
     * Set SEO keywords
     *
     * @author TB
     * @date 30.4.2025
     *
     * @param string $keywords
     *
     * @return void
     */
    public function keywords(string $keywords): void {
        $this->_view->set_keywords($keywords);
    }

    /**
     * Set realm name
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $realm
     *
     * @return void
     */
    public function set_realm(string $realm): void {
        $this->_realm = $realm;
        $this->_view->set_realm($realm);
    }

    /**
     * Set controller name
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $singular
     *
     * @return void
     */
    public function set_name(string $singular): void {
        $this->_name = $singular;
    }

    /**
     * Set controller name in plural
     *
     * @author TB
     * @date 26.4.2025
     *
     * @param string $plural
     *
     * @return void
     */
    public function set_name_plural(string $plural): void {
        $this->_name_plural = $plural;
    }

    public function add() : bool {

        $this->title(__tx('Add %s', $this->_name));

        __t('Add %s', $this->_name);

        return true;
    }

    public function edit(mixed $id = null) : bool {

        $this->title(__tx('Edit %s', $this->_name));

        __t('Edit %s (ID: %s)', $this->_name, $id);

        return true;
    }

    public function delete(mixed $id = null) : void {

        $this->title(__tx('Delete %s', $this->_name));

        __t('Delete %s (ID: %s)', $this->_name, $id);

    }

    public function view(mixed $id = null) : void {

        $this->title(__tx('View %s', $this->_name));

        __t('View %s (ID: %s)', $this->_name, $id);
    }

    public function list() : void {

        $this->title(__tx('%s', ucfirst($this->_name_plural)));

//        __t('Listing %s on page %s', $this->_name_plural, $page);


//        if ($this->_model !== null) {
//            $this->_model->page($page);
//        }




        // Here we'll later load view templates automatically
    }

    /**
     * Index method - default action
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return void
     */
    public function index() : void {
        $this->list();
    }

    /**
     * Assign a value to the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return void
     */
    protected function assign(string $key, mixed $value): void {
        $this->_view->assign($key, $value);
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
    protected function master(string $master_page): void {
        $this->_view->master($master_page);
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
    protected function css(string $path): void {
        $this->_view->css($path);
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
    protected function js(string $path): void {
        $this->_view->js($path);
    }

    /**
     * Enable/disable rendering of the view
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param bool $enabled
     *
     * @return void
     */
    protected function set_render(bool $enabled) : void {
        $this->_is_render = $enabled;
    }

    /**
     * Check if rendering is enabled
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return bool
     */
    public function is_render() : bool {
        return $this->_is_render;
    }

    /**
     * Render the view
     *
     * @author TB
     * @date 26.4.2025
     *
     * @return void
     *
     * @throws Exception
     */
    public function render(): void {

        if ($this->_model !== null) {

            $this->_view->set_pagination(new Pagination(
                $this->_model->get_page(),
                $this->_model->get_per_page(),
                $this->_model->get_total()
            ));
        }

        try {
            $this->_view->render();
        } catch (LumioViewException $e) {

            $this->assign('error_message', $e->getMessage());

            $this->_view->set_realm('lumio');
            $this->_view->set_controller('error');
            $this->set_action('view');
            $this->title(__tx('View error'));

            try {
                $this->render();
                $this->set_render(false);
            } catch (\Throwable $e) {
                $this->response()->status($e->getCode())->body($e->getMessage())->fail();
            }

            return;

        } catch (Exception $e) {
            $this->response()->status($e->getCode())->body($e->getMessage())->fail();
        }
    }

    /**
     * Actions to be performed before the action method is called
     *
     * @author TB
     * @date 30.4.2025
     *
     * @return void
     *
     * @throws LumioControllerException
     * @throws Exception
     */
    private function _before(): void {

        $this->_set_csrf();

        $this->_check_action_exists();

        $this->_check_user_permission();
        $this->_check_user_verification();

        $this->_validate_request_data();

        Flash::absorb();
    }

    /**
     * Actions to be performed after the action method is called
     *
     * @author TB
     * @date 30.4.2025
     *
     * @return void
     */
    private function _after(): void {

        $resolver = $this->container()->get(RouteResolver::class);

        $realm = $resolver->get_realm();
        $controller = $resolver->get_controller();
        $action = $resolver->get_action();

        History::push($controller, $action, $this->_title, $this->_request, $realm);



        $breadcrumb = $this->_container->get(BreadcrumbBuilder::class);
        $this->_view->set_breadcrumb($breadcrumb->get());


    }

    /**
     * Set CSRF token
     *
     * @author TB
     * @date 19.5.2025
     *
     * @return void
     *
     * @throws Exception
     */
    protected function _set_csrf(): void {

        $csrf = $this->container()->get(CSRF::class);
        $this->set_csrf($csrf);
    }

    /**
     * Check if the action exists in controller
     *
     * @author TB
     * @date 30.4.2025
     *
     * @return void
     *
     * @throws LumioControllerException
     */
    protected function _check_action_exists(): void {

        if (!method_exists($this, $this->_action)) {
            throw new LumioControllerException(__tx('Action "%s" was not found', $this->_action), Response::HTTP_404);
        }
    }

    protected function _check_user_permission(): void {

//        Logger::channel('request')->info('Verification of user permissions');

        // TODO: service class for user permissions based on role - it has access to db through injected db adapter instance
    }

    protected function _check_user_verification(): void {

        // TODO: service class for user verifications - it has access to db through injected db adapter instance
    }

    /**
     * Validate request data
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return void
     */
    protected function _validate_request_data(): void {

        if ($this->_model !== null) {
            RequestValidator::validate($this->_request, $this->_model);
        }
    }

    /**
     * Check if the request is valid
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return bool
     */
    protected function is_valid() : bool {
        return RequestValidator::is_valid();
    }

    /**
     * Get value from $_GET
     *
     * @author TB
     * @date 5.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function get(?string $key = null, mixed $default = null) : mixed {
        return $this->_request->get($key, $default);
    }

    /**
     * Get value from $_POST
     *
     * @author TB
     * @date 5.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function post(?string $key = null, mixed $default = null) : mixed {
        return $this->_request->post($key, $default);
    }

    /**
     * Get value from filter data
     *
     * @author TB
     * @date 11.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function filter(?string $key = null, mixed $default = null) : mixed {
        return $this->_request->filter($key, $default);
    }

    /**
     * Get uploaded file from request
     *
     * @author TB
     * @date 28.5.2025
     *
     * @param string $key
     *
     * @return UploadedFile|array|null
     */
    protected function file(string $key): null|UploadedFile|array {
        return $this->_request->file($key);
    }

    /**
     * Get value from JSON data
     *
     * @author TB
     * @date 17.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function get_json(?string $key = null, mixed $default = null): mixed {
        return $this->_request->json($key, $default);
    }

    /**
     * Get value from XML data
     *
     * @author TB
     * @date 17.5.2025
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function get_xml(?string $key = null, mixed $default = null): mixed {
        return $this->_request->xml($key, $default);
    }

    /**
     * Get the response object
     *
     * @author TB
     * @date 5.5.2025
     *
     * @return Response
     */
    protected function response() : Response {
        return $this->_response;
    }

    /**
     * Replace the controller request object with the given one
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param Request $request
     *
     * @return void
     */
    public function replace_request(Request $request) : void {

        $this->_request = $request;

        $this->_set_input_data2model();
    }

    /**
     * Creates a redirect response that is to be returned from inside the controller
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param string $url
     * @param int $status_code
     *
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status_code = Response::HTTP_302): RedirectResponse {
        return new RedirectResponse($url, $status_code);
    }

    /**
     * Creates a JSON response that is to be returned from inside the controller
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param array $data
     *
     * @return JsonResponse
     */
    protected function json(array $data) : JsonResponse {
        return new JsonResponse($data);
    }

    /**
     * Creates an XML response that is to be returned from inside the controller
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param array $data
     *
     * @return XmlResponse
     */
    protected function xml(array $data) : XmlResponse {
        return new XmlResponse($data);
    }

    /**
     * Creates a file response that is to be returned from inside the controller
     *
     * This is used for downloading files, such as images, documents, etc.
     *
     * @author TB
     * @date 30.5.2025
     *
     * @param string $file_path
     * @param string|null $file_name
     *
     * @return FileResponse
     */
    protected function download_file(string $file_path, ?string $file_name = null): FileResponse {
        return new FileResponse($file_path, $file_name);
    }

    /**
     * Set given CSRF protection
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param CSRF $csrf
     *
     * @return void
     */
    public function set_csrf(CSRF $csrf): void {

        $this->_csrf_token = $csrf->get_token();
        $this->_csrf_field = $csrf->get_field();

        $this->_view->set_csrf($this->_csrf_token, $this->_csrf_field);
    }

    /**
     * Get the breadcrumb builder singleton instance
     *
     * @author TB
     * @date 19.5.2025
     *
     * @param BreadcrumbItem $item
     *
     * @return void
     *
     * @throws Exception
     */
    protected function breadcrumb(BreadcrumbItem $item): void {
        $bc = $this->_container->get(BreadcrumbBuilder::class);
        $bc->add($item);
    }

    /**
     * Post factory and post container-pass hook.
     * This happens right when the controller is ready, set up but before any request processing.
     * Actions that would normally be done in the constructor but need container should be done here.
     *
     * @author TB
     * @date 21.5.2025
     *
     * @return void
     */
    public function ignite(): void {

        // empty placeholder for overriding
    }

    /**
     * Set given Open Graph data
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param array $data
     *
     * @return void
     */
    protected function set_og(array $data): void {
        $this->_view->set_og($data);
    }

    /**
     * Set given Open Graph property
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    protected function og(string $key, string $value): void {
        $this->_view->set_og_property($key, $value);
    }

    /**
     * Set Open Graph title
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_title(string $value): void {
        $this->_view->og_title($value);
    }

    /**
     * Set Open Graph description
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_description(string $value): void {
        $this->_view->og_description($value);
    }

    /**
     * Set Open Graph image
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_image(string $value): void {
        $this->_view->og_image($value);
    }

    /**
     * Set Open Graph URL
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_url(string $value): void {
        $this->_view->og_url($value);
    }

    /**
     * Set Open Graph type
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_type(string $value): void {
        $this->_view->og_type($value);
    }

    /**
     * Set Open Graph locale
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_locale(string $value): void {
        $this->_view->og_locale($value);
    }

    /**
     * Set Open Graph locale
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_site_name(string $value): void {
        $this->_view->og_site_name($value);
    }

    /**
     * Set Open Graph updated time
     *
     * @author TB
     * @date 22.5.2025
     *
     * @param string $value
     *
     * @return void
     */
    protected function og_updated_time(string $value): void {
        $this->_view->og_updated_time($value);
    }

    /**
     * Get the database logger instance
     *
     * @author TB
     * @date 26.5.2025
     *
     * @return DatabaseLoggerProxy
     *
     * @throws Exception
     */
    protected function log(): DatabaseLoggerProxy {

        if ($this->_container === null) {
            throw new Exception('Container is not set in the controller - cannot log');
        }

        $logger = $this->_container->get(DatabaseLoggerProxy::class);
        $logger->model($this->_name);

        return $logger;
    }

}
