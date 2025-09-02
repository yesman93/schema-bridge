<?php

namespace Lumio\DTO\IO;

use Exception;
use Lumio\Container;
use Lumio\Exceptions\LumioViewException;
use Lumio\View\View;
use Lumio\Traits;

class ViewResponse {

    use Traits\View\Master;
    use Traits\IO\HttpStatus;

    /**
     * View object
     *
     *
     * @var View
     */
    private View $_view;

    /**
     * View response
     *
     *
     * @param Container $container
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(Container $container) {

        $this->_view = $container->get(View::class);
    }

    /**
     * Set realm
     *
     *
     * @param string $realm
     *
     * @return self
     */
    public function realm(string $realm): self {
        $this->_view->set_realm($realm);
        return $this;
    }

    /**
     * Set controller
     *
     *
     * @param string $controller
     *
     * @return self
     */
    public function controller(string $controller): self {
        $this->_view->set_controller($controller);
        return $this;
    }

    /**
     * Set action
     *
     *
     * @param string $action
     *
     * @return self
     */
    public function action(string $action): self {
        $this->_view->set_action($action);
        return $this;
    }

    /**
     * Set title
     *
     *
     * @param string $title
     *
     * @return self
     */
    public function title(string $title): self {
        $this->_view->title($title);
        return $this;
    }

    /**
     * Assign a variable to view
     *
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function assign(string $key, mixed $value): self {
        $this->_view->assign($key, $value);
        return $this;
    }

    /**
     * Set master page
     *
     *
     * @param string $master
     *
     * @return self
     *
     * @throws LumioViewException
     */
    public function master(string $master): self {
        $this->_view->master($master);
        return $this;
    }

    /**
     * Render the view
     *
     *
     * @return void
     *
     * @throws Exception
     */
    public function render(): void {
        $this->_view->render();
        exit;
    }

}
