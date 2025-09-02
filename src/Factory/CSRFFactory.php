<?php

namespace Lumio\Factory;

use Exception;
use Lumio\Container;
use Lumio\Security\CSRF;

class CSRFFactory {

    /**
     * Instance of the container
     *
     * @author TB
     * @date 18.5.2025
     *
     * @var Container
     */
    private Container $_container;

    /**
     * Factory for creating CSRF instances
     *
     * @author TB
     * @date 18.5.2025
     *
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->_container = $container;
    }

    /**
     * Create a new CSRF instance
     *
     * @author TB
     * @date 18.5.2025
     *
     * @return CSRF
     *
     * @throws Exception
     */
    public function make(): CSRF {

        try {
            $instance = new CSRF($this->_container);
            $instance->generate();
        } catch (Exception $e) {
            throw new Exception('Failed to create CSRF instance: ' . $e->getMessage());
        }

        return $instance;
    }

}
