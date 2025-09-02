<?php

namespace App\Controllers;

use Lumio\Controller\BaseController;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Model\BaseModel;
use Lumio\View\View;

class PageController extends BaseController {

    /**
     * Controller for pages
     *
     * @author TB
     * @date 30.4.2025
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

    public function resolve_page(?string $page = null) {

        // TODO: check if exists page as method, if not, check in the database and if not, display 404

        $this->set_action($page);

        $this->title(ucfirst($page));

    }

    public function view_page(string $page) {

        // TODO: if page is in DB, this is called - here it loads the content and SEO

    }

    public function index() : void {

        $this->home();
        $this->set_action('home');
    }

    public function home() {

        $this->title(__tx('Homepage'));


    }

}
