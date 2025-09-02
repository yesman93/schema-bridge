<?php

namespace Lumio\Factory;

use Lumio\View\Component;
use Lumio\View\View;
use Lumio\IO\Request;

class ViewFactory {

    /**
     * Make a view
     *
     * @param string $controller
     * @param string $action
     * @param Request $request
     * @param array $params
     * @param string|null $realm
     *
     * @return View
     */
    public function make(string $controller, string $action, Request $request, array $params = [], ?string $realm = null): View {

        $view = new View($controller, $action, $request);

        if (!empty($realm)) {
            $view->set_realm($realm);
        }

        if (!empty($params)) {
            $view->set_params($params);
        }

        Component::set_controller($controller);
        Component::set_action($action);
        Component::set_request($request);

        return $view;
    }
}
