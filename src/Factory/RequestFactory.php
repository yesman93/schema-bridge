<?php

namespace Lumio\Factory;

use Lumio\IO\Request;

class RequestFactory {

    /**
     * Make a new request
     *
     * @return Request
     */
    public function make() : Request {

        $request = new Request();
        $request->generate_id();

        return $request;
    }

}
