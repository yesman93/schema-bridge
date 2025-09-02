<?php

namespace App\Controllers\Lumio;

use Lumio\Container;
use Lumio\Controller\BaseController;
use Lumio\Exceptions\LumioPaginationTotalException;
use Lumio\Factory\RouterFactory;
use Lumio\IO\URIParamsParser;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Model\BaseModel;
use Lumio\Router;
use Lumio\Routing\RouteResolver;
use Lumio\View\View;

class ListviewController extends BaseController {

    /**
     * controller for listview
     *
     * @author TB
     * @date 6.5.2025
     *
     * @param BaseModel|null $model
     * @param Request $request
     * @param Response $response
     *
     * @param View $view
     */
    public function __construct(?BaseModel $model, Request $request, Response $response, View $view) {
        parent::__construct($model, $request, $response, $view);
    }

    /**
     * Get pagination
     *
     * @author TB
     * @date 6.5.2025
     *
     * @return void
     */
    public function pagination() : void {

        $this->master(View::MASTER_EMPTY);

        $uri = $this->post('uri');
        if (empty($uri)) {
            $this->response()->status(Response::HTTP_400)->body('Missing URI')->send();
            return;
        }

        $container = Container::setup();
        $router = (new RouterFactory())->make(
            $container,
            $uri
        );

        $realm_name = $router->get_realm_name();
        $controller_name = $router->get_controller_name();

        $controller = $router->get_controller();
        $action = $router->get_action();
        $params = $router->get_params();

        $controller->set_name($router->get_controller_name());
        $controller->set_name_plural($router->get_controller_name_plural());

        // Need the request from the router instance dedicated to given URI, not the one for current action
        $request = $container->get(Request::class);

        $page       = $request->get_page() ?? 1;
        $filters    = $request->filter() ?? [];
        $sorting    = $request->get_sorting();

        // Extract page, filters and sorting from parameters and set into request
        URIParamsParser::parse($params, $request);

        $request->set_page(BaseModel::PAGE_GET_TOTAL);

        $controller->replace_request($request);

        $total = 0;
        $per_page = 0;
        try {
            call_user_func_array([$controller, $action], $params);
        } catch (LumioPaginationTotalException $e) {
            $total = $e->get_total();
            $per_page = $e->get_per_page();
        } catch (\Throwable $e) {
            $this->response()->status(Response::HTTP_500)->body($e->getMessage())->send();
            return;
        }

        $base_uri = empty($realm_name) ? '' : $realm_name . '/';
        $base_uri .= $controller_name;
        $base_uri .= '/';
        $base_uri .= $action;

//        vdump([
//            $page,
//            $per_page,
//            $total,
//            $base_uri,
//            URIParamsParser::build_filter($filters),
//            URIParamsParser::build_sorting($sorting),
//            $params
//        ]);

        $this->assign('page', $page);
        $this->assign('per_page', $per_page);
        $this->assign('total', $total);
        $this->assign('base_uri', $base_uri);
        $this->assign('filters', URIParamsParser::build_filter($filters));
        $this->assign('sorting', URIParamsParser::build_sorting($sorting));
        $this->assign('params', $params);
    }

}
