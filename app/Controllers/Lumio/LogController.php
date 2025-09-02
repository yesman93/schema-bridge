<?php

namespace App\Controllers\Lumio;

use Lumio\Config;
use Lumio\Controller\BaseController;
use Lumio\IO\URIParamsParser;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Log\Logger;
use Lumio\Log\LogReader;
use Lumio\Model\BaseModel;
use Lumio\Routing\RouteResolver;
use Lumio\View\View;

class LogController extends BaseController {

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

    public function logs() {

        $this->master(View::MASTER_PRIVATE);
        $this->title(__tx('Logs'));

        try {
            $channels = Config::get('app.logging.channels');
            $channels = array_keys($channels);
        } catch (\Exception $e) {
            $channels = [];
        }

        $this->assign('channels', $channels);

        $channel = $this->filter('channel');
        if (empty($channel)) {
            $channel = reset($channels);
        }

        $this->assign('channel', $channel);

        $log_file = $this->filter('log_file');

        $logs = LogReader::read($channel, $log_file);
        $this->assign('logs', $logs);


    }

}
