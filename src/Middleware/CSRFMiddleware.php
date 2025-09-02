<?php

namespace Lumio\Middleware;

use Exception;
use Lumio\Container;
use Lumio\DTO\IO\JsonResponse;
use Lumio\DTO\IO\XmlResponse;
use Lumio\IO\Request;
use Lumio\Security\CSRF;
use Lumio\View\View;
use Lumio\Traits;

class CSRFMiddleware {

    use Traits\IO\HttpStatus;

    /**
     * Handle CSRF validation
     *
     *
     * @param Container $container
     * @param callable $next
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle(Container $container, callable $next): mixed {

        $csrf = $container->get(CSRF::class);

        if (!$csrf->is_enabled() || $csrf->is_exception()) {
            return $next();
        }

        $request = $container->get(Request::class);

        $method = $request->server('REQUEST_METHOD') ?? 'GET';

        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {

            $token = $csrf->get_token_from_request();

            if (empty($token) || !$csrf->validate($token)) {

                if ($request->expects_json()) {

                    return new JsonResponse([
                        'error' => __tx('CSRF validation failed'),
                        'code' => 403,
                    ]);
                }

                if ($request->expects_xml()) {

                    return new XmlResponse([
                        'error' => __tx('CSRF validation failed'),
                        'code' => 403,
                    ]);
                }

                $view = $container->get(View::class);

                $view->assign('error_message', __tx('Token invalid or expired'));
                $view->assign('error_code', self::HTTP_403);

                $view->master(View::MASTER_PUBLIC);
                $view->set_realm('lumio');
                $view->set_controller('error');
                $view->set_action('csrf');
                $view->title(__tx('CSRF validation failed'));

                $view->render();

                exit;
            }
        }

        return $next();
    }

}
