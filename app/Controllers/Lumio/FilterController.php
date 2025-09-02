<?php

namespace App\Controllers\Lumio;

use Lumio\Controller\BaseController;
use Lumio\IO\URIParamsParser;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Model\BaseModel;
use Lumio\Routing\RouteResolver;
use Lumio\View\View;

class FilterController extends BaseController {

    /**
     * controller for filters
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
     * Remove a filter from URI
     *
     * @return void
     */
    public function remove() : void {

        $this->set_render(false);

        $uri = $this->post('uri');
        if (empty($uri)) {
            $this->response()->status(Response::HTTP_400)->body('Missing URI')->send();
            return;
        }

        $filter = $this->post('filter');
        if (empty($filter)) {
            $this->response()->status(Response::HTTP_200)->body($uri)->send();
            return;
        }

        $request = clone $this->_request;

        try {
            $resolver = new RouteResolver($request, $uri);
        } catch (\Throwable $e) {
            $this->response()->status(Response::HTTP_200)->body($uri)->send();
            return;
        }

        $realm = $resolver->get_realm() ?? '';
        $controller = $resolver->get_controller() ?? '';
        $action = $resolver->get_action() ?? '';
        $params = $resolver->get_params() ?? [];

        URIParamsParser::parse($params, $request);

        $filter_data = $request->filter() ?? [];
        unset($filter_data[$filter]);
        $request->replace_filter_data($filter_data);

        $link = URIParamsParser::build($controller, $action, $request, $realm, $params);

        $this->response()->status(Response::HTTP_200)->body($link)->send();
    }

    public function set() {

        $this->set_render(false);

        $uri = $this->post('uri');
        if (empty($uri)) {
            $this->response()->status(Response::HTTP_400)->body('Missing URI')->send();
            return;
        }

        $filter_name = $this->post('filter_name');
        $filter_value = $this->post('filter_value');
        if (empty($filter_name)) {
            $this->response()->status(Response::HTTP_200)->body($uri)->send();
            return;
        }

        $json = json_decode($filter_value, true);
        if (is_array($json)) {
            $filter_value = $json;
        }

        $request = clone $this->_request;

        try {
            $resolver = new RouteResolver($request, $uri);
        } catch (\Throwable $e) {
            $this->response()->status(Response::HTTP_200)->body($uri)->send();
            return;
        }

        $realm = $resolver->get_realm() ?? '';
        $controller = $resolver->get_controller() ?? '';
        $action = $resolver->get_action() ?? '';
        $params = $resolver->get_params() ?? [];

        URIParamsParser::parse($params, $request);

        $filter_data = $request->filter() ?? [];
        $filter_data[$filter_name] = $filter_value;
        $request->replace_filter_data($filter_data);

        $link = URIParamsParser::build($controller, $action, $request, $realm, $params);

        $this->response()->status(Response::HTTP_200)->body($link)->send();
    }

}
