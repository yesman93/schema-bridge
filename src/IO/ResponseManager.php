<?php

namespace Lumio\IO;

use Exception;
use Lumio\DTO\IO\FileResponse;
use Lumio\DTO\IO\RedirectResponse;
use Lumio\DTO\IO\JsonResponse;
use Lumio\DTO\IO\ViewResponse;
use Lumio\DTO\IO\XmlResponse;
use Lumio\Exceptions\LumioViewException;
use Lumio\Utilities\Session;
use Lumio\Config;
use Lumio\Container;
use Lumio\View\View;

class ResponseManager {

    /**
     * instance of the container
     *
     *
     * @var Container
     */
    private Container $_container;

    /**
     * Response management
     *
     *
     * @param Container $container
     *
     * @return void
     */
    public function __construct(Container $container) {
        $this->_container = $container;
    }

    /**
     * Prepare response
     *
     *
     * @param mixed $result
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function prepare_response(mixed $result): mixed {

        $request = $this->_container->get(Request::class);

        $is_array = is_array($result);
        $is_object = is_object($result);
        $is_json = $result instanceof JsonResponse;
        $is_xml = $result instanceof XmlResponse;
        $is_file = $result instanceof FileResponse;

        if ($request->expects_json()) {

            if (!$is_json && !$is_array && !$is_object) {

                $result = new JsonResponse([
                    'error' => __tx('Invalid response type'),
                    'expected_format' => 'json',
                ]);

            } else if (!$is_json) {
                $result = new JsonResponse((array) $result);
            }

            return $this->_prepare_response_json($result);
        }

        if ($request->expects_xml()) {

            if (!$is_xml && !$is_array && !$is_object) {

                $result = new XmlResponse([
                    'error' => __tx('Invalid response type'),
                    'expected_format' => 'xml',
                ]);

            } else if (!$is_xml) {
                $result = new XmlResponse((array) $result);
            }

            return $this->_prepare_response_xml($result);
        }

        if ($result instanceof RedirectResponse) {
            return $this->_prepare_response_redirect($result);
        }

        if (($is_array || $is_object) && !$is_json && !$is_xml && !$is_file) {
            $result = new JsonResponse((array) $result);
            return $this->_prepare_response_json($result);
        } else if ($is_json) {
            return $this->_prepare_response_json($result);
        } else if ($is_xml) {
            return $this->_prepare_response_xml($result);
        } else if ($is_file) {
            return $this->_prepare_response_file($result);
        }

        return null;
    }

    /**
     * Prepare redirect response
     *
     *
     * @param RedirectResponse $result
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function _prepare_response_redirect(RedirectResponse $result): mixed {

        if (!$result->is_allowed_host()) {

            return (new ViewResponse($this->_container))

                ->realm('lumio')
                ->controller('error')
                ->action('invalid_url')

                ->master(View::MASTER_PUBLIC)
                ->title(__tx('Host not allowed'))

                ->assign('error_message', __tx('Outgoing host "%s" not allowed', $result->get_host()))
                ->assign('error_code', ViewResponse::HTTP_403);
        }

        $messages = [
            'errors' => Flash::get_errors() ?? [],
            'warnings' => Flash::get_warnings() ?? [],
            'infos' => Flash::get_infos() ?? [],
            'successes' => Flash::get_successes() ?? [],
        ];

        $session_key = Config::get('app.view.flash_messages_session');
        Session::set($session_key, $messages);

        $response = $this->_container->get(Response::class);
        $response->clear();

        return $response
            ->status($result->get_status_code())
            ->header(['Location' => $result->get_url()]);
    }

    /**
     * Prepare JSON response
     *
     *
     * @param JsonResponse $result
     *
     * @return Response
     *
     * @throws Exception
     */
    private function _prepare_response_json(JsonResponse $result): Response {

        $response = $this->_container->get(Response::class);
        $response->clear();

        return $response
            ->status(JsonResponse::HTTP_200)
            ->header(['Content-type' => 'application/json'])
            ->header(['Content-Length' => $result->get_length()])
            ->header(['Cache-Control' => 'no-store, no-cache, must-revalidate'])
            ->header(['Pragma' => 'no-cache'])
            ->header(['Expires' => '0'])
            ->body((string) $result);
    }

    /**
     * Prepare XML response
     *
     *
     * @param XmlResponse $result
     *
     * @return Response
     *
     * @throws Exception
     */
    private function _prepare_response_xml(XmlResponse $result): Response {

        $response = $this->_container->get(Response::class);
        $response->clear();

        return $response
            ->status(XmlResponse::HTTP_200)
            ->header(['Content-type' => 'application/xml'])
            ->header(['Content-Length' => $result->get_length()])
            ->header(['Cache-Control' => 'no-store, no-cache, must-revalidate'])
            ->header(['Pragma' => 'no-cache'])
            ->header(['Expires' => '0'])
            ->body((string) $result);
    }

    /**
     * Prepare file response
     *
     *
     * @param FileResponse $result
     *
     * @return Response
     *
     * @throws Exception
     */
    private function _prepare_response_file(FileResponse $result): Response {

        $response = $this->_container->get(Response::class);
        $response->clear();

        return $response
            ->status(FileResponse::HTTP_200)
            ->header(['Content-Description' => 'File Transfer'])
            ->header(['Content-Type' => $result->get_mime_type()])
            ->header(['Content-Length' => $result->get_size()])
            ->header(['Content-Disposition' => 'attachment; filename="' . $result->get_file_name() . '"'])
            ->header(['Content-Transfer-Encoding' => 'binary'])
            ->header(['Expires' => '0'])
            ->header(['Cache-Control' => 'must-revalidate'])
            ->header(['Pragma' => 'public']);
    }

    /**
     * Respond to the given controller result
     *
     *
     * @param mixed $result
     *
     * @return void
     *
     * @throws LumioViewException
     */
    public function respond(mixed $result): void {

        try {
            $response = $this->prepare_response($result);
        } catch (Exception $e) {
            $response = new ViewResponse($this->_container);
            $response->master(View::MASTER_PUBLIC);
        }

        if ($response instanceof Response) {

            if ($response->is_download()) {

                $file_path = $result->get_file_path();

                ob_end_clean();
                $response->send_headers();
                readfile($file_path);

                exit;

            } else {
                $response->send();
            }

        } else if ($response instanceof ViewResponse) {
            $response->render();
        }
    }

}
