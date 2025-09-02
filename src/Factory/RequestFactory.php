<?php

namespace Lumio\Factory;

use Lumio\IO\Request;

class RequestFactory {

    /**
     * Make a new request
     *
     * @author TB
     * @date 7.5.2025
     *
     * @return Request
     */
    public function make() : Request {

        $request = new Request();
        $request->generate_id();

        return $request;
    }

}
