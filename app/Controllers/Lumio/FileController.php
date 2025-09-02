<?php

namespace App\Controllers\Lumio;

use Lumio\Config;
use Lumio\Controller\BaseController;
use Lumio\File\Storage;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Log\Logger;
use Lumio\Model\BaseModel;
use Lumio\View\View;
use Throwable;

class FileController extends BaseController {

    /**
     * controller for filters
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

    public function show(?string $token) {

        $path_placeholder = Config::get('app.storage.private.filepath_placeholder');

        try {

            if (empty($token)) {
                Logger::channel('app')->warning('Missing token');
                $path = $path_placeholder;
            } else {

                $path = Storage::resolve_token($token);
                if (!$path || !is_file($path) || !is_readable($path)) {
                    Logger::channel('app')->warning('File not found', ['token' => $token, 'path' => $path]);
                    $path = $path_placeholder;
                }
            }

        } catch (Throwable $e) {
            $path = $path_placeholder;
        }

        $size = filesize($path);
        $mime = mime_content_type($path);

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . $size);
        header('Cache-Control: private, max-age=86400');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        header('Pragma: public');

        readfile($path);
        exit;
    }


}
