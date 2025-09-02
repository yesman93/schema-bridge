<?php

namespace App\Controllers;

use App\Models\LanguageModel;
use App\Models\PackageModel;
use Exception;
use Lumio\Database\DatabaseAdapter;
use Lumio\DTO\Model\Sorting;
use Lumio\Exceptions\LumioViewException;
use Throwable;
use Lumio\Controller\BaseController;
use Lumio\DTO\View\BreadcrumbItem;
use Lumio\Exceptions\LumioDatabaseException;
use Lumio\Exceptions\LumioValidationException;
use Lumio\IO\MessageBag;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Log\Logger;
use Lumio\Model\BaseModel;
use Lumio\View\View;

class PageController extends BaseController
{

    /**
     * Controller for pages
     *
     * @param BaseModel|null $model
     * @param Request $request
     * @param Response $response
     * @param View $view
     *
     * @return void
     */
    public function __construct(?BaseModel $model, Request $request, Response $response, View $view) {
        parent::__construct($model, $request, $response, $view);
    }

    /**
     * Initial hook of the controller
     *
     * @return void
     *
     * @throws Exception
     */
    public function ignite(): void
    {
        $this->breadcrumb(new BreadcrumbItem('/page/pages', __tx('Pages')));
    }

    /**
     * Outline of pages
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function pages(): mixed
    {

        parent::list();

        $this->master(View::MASTER_PRIVATE);

        $pages = $this->_model->all();
        $this->assign('pages', $pages);

        return null;
    }

    /**
     * Prepare add/edit form
     *
     * @return void
     *
     * @throws Exception
     */
    private function _prepare_addedit(): void
    {

        $this->asset('tinymce');

        $db = $this->container()->get(DatabaseAdapter::class);

        $languages4select = (new LanguageModel($db))->get_choices('iso', 'name');
        $this->assign('languages4select', $languages4select);
    }

    /**
     * Add page
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function add(): mixed
    {

        parent::add();

        $this->master(View::MASTER_MODAL);

        $this->_prepare_addedit();

        if (!$this->is_submit()) {
            return false;
        }

        if (!$this->is_valid()) {
            return false;
        }

        try {
            $page_id = $this->_model->add();
            if ($page_id > 0) {
                $this->log()->info('Added page with ID: ' . $page_id);
                return $this->close_modal('/page/pages')->success(__tx('%s successfully added!$', __tx('Page')));
            }

            MessageBag::error(__tx('%s could not be added!', __tx('Page')));

            return false;
        } catch (LumioValidationException $e) {
            MessageBag::error($e->getMessage());
            return false;
        } catch (LumioDatabaseException $e) {
            $code = $e->get_code();
            Logger::channel('db')->error('Error adding page, code: ' . $code . ', message: ' . $e->getMessage());
            MessageBag::error(__tx('Failed to add %s, contact technical support with code %s', __tx('page'), $code));
            return false;
        } catch (Throwable $e) {
            vdump($e->getFile() . ':' . $e->getLine());
            vdump($e->getTrace());
            Logger::channel('error')->error('Error adding page: ' . $e->getMessage());
            MessageBag::error(__tx('An unexpected error occurred while adding %s, contact technical support', __tx('page')));
            return false;
        }
    }

    /**
     * Edit page
     *
     * @param mixed $id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function edit(mixed $id = null): mixed
    {

        $destination = $this->close_modal(true);

        try {
            $page = $this->_model->get($id);
        } catch (Throwable $e) {
            $page = [];
        }

        if ($page === []) {
            return $destination->error(__tx('%s not found!', __tx('Page')));
        }

        $this->assign('page', $page);

        parent::edit($id);

        $this->master(View::MASTER_MODAL);

        $this->_prepare_addedit();

        if (!$this->is_submit()) {
            return false;
        }

        if (!$this->is_valid()) {
            return false;
        }

        try {
            $page_id = $this->_model->edit();
            if ($page_id > 0) {
                $this->log()->info('Modified page with ID: ' . $page_id);
                return $destination->success(__tx('%s successfully modified!', __tx('Page')));
            }

            MessageBag::error(__tx('%s could not be modified!', __tx('Page')));

            return false;
        } catch (LumioValidationException $e) {
            MessageBag::error($e->getMessage());
            return false;
        } catch (LumioDatabaseException $e) {
            $code = $e->get_code();
            Logger::channel('db')->error('Error modifying page, code: ' . $code . ', message: ' . $e->getMessage());
            MessageBag::error(__tx('Failed to modify %s, contact technical support with code %s', __tx('page'), $code));
            return false;
        } catch (Throwable $e) {
            Logger::channel('error')->error('Error modifying page: ' . $e->getMessage());
            MessageBag::error(__tx('An unexpected error occurred while modifying %s, contact technical support', __tx('page')));
            return false;
        }
    }

    /**
     * Delete page
     *
     * @param mixed $id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function delete(mixed $id = null): mixed
    {

        $destination = $this->redirect('/page/pages');

        $page = $this->_model->get($id);
        if ($page === []) {
            return $destination->error(__tx('%s not found!', __tx('Page')));
        }

        try {
            if ($this->_model->remove($id)) {
                $this->log()->info(__tx('Deleted page with ID: %s', $id));
                return $destination->success(__tx('%s successfully deleted!', __tx('Page')));
            } else {
                return $destination->error(__tx('Failed to delete %s!', __tx('page')));
            }
        } catch (LumioValidationException $e) {
            return $destination->error($e->getMessage());
        } catch (LumioDatabaseException $e) {
            $code = $e->get_code();
            Logger::channel('db')->error('Error deleting setting, code: ' . $code . ', message: ' . $e->getMessage());
            return $destination->error(__tx('Failed to delete %s, contact technical support with code %s', __tx('page'), $code));
        } catch (Throwable $e) {
            Logger::channel('error')->error('Error deleting setting: ' . $e->getMessage());
            return $destination->error(__tx('An unexpected error occurred while deleting %s, contact technical support', __tx('page')));
        }
    }

    public function resolve_page(?string $page = null)
    {

        if (method_exists($this, $page)) {
            $this->set_action($page);
            $this->title(ucfirst($page));
            call_user_func_array([$this, $page], []);
        } else {
            $this->view_page($page);
        }
    }

    public function view_page(string $page)
    {

        $page = $this->_model->where('url', '=', $page)->row();
        if ($page === []) {
            throw new LumioViewException(__tx('Page not found'));
        }

        $this->title($page['title']);
        $this->keywords($page['keywords']);
        $this->description($page['description']);

        $this->assign('page', $page);
    }

    public function index(): mixed
    {
        return $this->redirect('/upload');

        $this->home();
        $this->set_action('home');

        return null;
    }

    public function home()
    {

        $this->title(__tx('Homepage'));
    }

    public function packages(): mixed
    {


        $this->title(__tx('Packages'));

        $db = $this->container()->get(DatabaseAdapter::class);
        $package_model = new PackageModel($db);

        $packages = $package_model->order_by('ord', Sorting::ASC)->all();
        $this->assign('packages', $packages);

        return null;
    }
}
