<?php

namespace Lumio\Middleware;

use Exception;
use Lumio\Contract\MiddlewareContract;
use Lumio\Container;
use Lumio\DTO\IO\JsonResponse;
use Lumio\DTO\IO\XmlResponse;
use Lumio\IO\Request;
use Lumio\Router;
use Lumio\Security\URLSigner;
use Lumio\Traits;
use Lumio\View\View;

class SignedUrlMiddleware implements MiddlewareContract {

    use Traits\IO\HttpStatus;

    /**
     * Handle signed URL validation
     *
     * @param Container $container
     * @param callable $next
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle(Container $container, callable $next): mixed {

        $router = $container->get(Router::class);

        $full_url = $router->get_full_url() ?? '';
        $is_signed = str_contains($full_url, '--');

        if ($is_signed && !URLSigner::is_valid($full_url)) {

            $request = $container->get(Request::class);


            if ($request->expects_json()) {

                return new JsonResponse([
                    'error' => __tx('Invalid URL'),
                    'code' => 403,
                ]);
            }


            if ($request->expects_xml()) {

                return new XmlResponse([
                    'error' => __tx('Invalid URL'),
                    'code' => 403,
                ]);
            }


            $view = $container->get(View::class);

            $view->assign('error_message', __tx('Invalid URL'));
            $view->assign('error_code', self::HTTP_403);

            $view->master(View::MASTER_PUBLIC);
            $view->set_realm('lumio');
            $view->set_controller('error');
            $view->set_action('invalid_url');
            $view->title(__tx('Invalid URL'));

            $view->render();

            exit;
        }

        return $next();
    }

}
