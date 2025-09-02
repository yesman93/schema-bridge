<?php

namespace Lumio;

use Lumio\Database\DatabaseAdapter;
use Lumio\Database\MysqlAdapter;
use Lumio\DTO\Database\DatabaseCredentials;
use Lumio\Exceptions\LumioDatabaseException;
use Lumio\Factory\ControllerFactory;
use Lumio\Factory\CSRFFactory;
use Lumio\Factory\ModelFactory;
use Lumio\Factory\RequestFactory;
use Lumio\Factory\RouterFactory;
use Lumio\Factory\ViewFactory;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\IO\ResponseManager;
use Lumio\Log\DatabaseLogger;
use Lumio\Log\DatabaseLoggerProxy;
use Lumio\Security\CSRF;
use Lumio\View\Helpers\BreadcrumbBuilder;

class Container {

    /**
     * bindings for the container
     *
     * @var array
     */
    private array $_bindings = [];

    /**
     * services
     *
     * @var array
     */
    private array $_services = [];

    /**
     * instances of services
     *
     * @var array
     */
    private array $_instances = [];

    /**
     * set service - saves the factory callable into the container
     *
     * @param string $service_name
     * @param callable $factory
     *
     * @return void
     */
    public function set(string $service_name, callable $factory): void {
        $this->_services[$service_name] = $factory;
    }

    /**
     * bind - binds the class name to the parameter map
     *
     * @param string $class_name
     * @param array $parameter_map
     *
     * @return void
     */
    public function bind(string $class_name, array $parameter_map): void {
        $this->_bindings[$class_name] = $parameter_map;
    }

    /**
     * returns the instance of the service - if the service is not created yet, it creates it
     *
     * @param string $service_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function get(string $service_name) {

        if (!isset($this->_instances[$service_name])) {

            // Manually registered factory
            if (isset($this->_services[$service_name])) {
                $this->_instances[$service_name] = ($this->_services[$service_name])();
                return $this->_instances[$service_name];
            }

            // Autowiring via reflection
            if (!class_exists($service_name)) {
                throw new \Exception('Service "' . $service_name . '" not found and not a valid class');
            }

            $reflection = new \ReflectionClass($service_name);
            $constructor = $reflection->getConstructor();

            if (is_null($constructor) || $constructor->getNumberOfParameters() === 0) {
                $instance = new $service_name();
            } else {

                $dependencies = [];

                foreach ($constructor->getParameters() as $param) {

                    $param_type = $param->getType();
                    $param_name = $param->getName();

                    // Class-based dependency
                    if ($param_type && !$param_type->isBuiltin()) {

                        $dependency_class = $param_type->getName();

                        // Check for bound scalar/class value
                        if (isset($this->_bindings[$service_name][$param_name])) {
                            $dependencies[] = $this->_bindings[$service_name][$param_name];
                        }

                        // Respect nullable parameters when no binding exists
                        else if ($param_type->allowsNull()) {
                            $dependencies[] = null;
                        }

                        // Attempt autowiring
                        else {
                            $dependencies[] = $this->get($dependency_class);
                        }

                        continue;
                    }

                    // Scalar binding
                    if (isset($this->_bindings[$service_name][$param_name])) {
                        $dependencies[] = $this->_bindings[$service_name][$param_name];
                        continue;
                    }

                    // Optional with default value
                    if ($param->isDefaultValueAvailable()) {
                        $dependencies[] = $param->getDefaultValue();
                        continue;
                    }

                    // Nullable scalar with no default and no binding - set to null
                    if ($param_type && $param_type->isBuiltin() && $param_type->allowsNull()) {
                        $dependencies[] = null;
                        continue;
                    }

                    // Cannot resolve
                    throw new \Exception('Cannot resolve scalar parameter "$' . $param_name . ' for service "' . $service_name . '"');
                }

                $instance = $reflection->newInstanceArgs($dependencies);
            }

            $this->_instances[$service_name] = $instance;
        }

        return $this->_instances[$service_name];
    }

    /**
     * checks if the service is registered in the container
     *
     * @param string $service_name
     *
     * @return bool
     */
    public function has(string $service_name): bool {
        return isset($this->_services[$service_name]);
    }

    /**
     * sets up the container instance and returns it
     *
     * @return Container
     */
    public static function setup() : Container {

        $instance = new self();

        $instance->set(Container::class, fn() => $instance);

        // Request
        $instance->set(Request::class, fn() => (new RequestFactory())->make());

        // Response
        $instance->set(Response::class, fn() => new Response());

        $instance->set(ResponseManager::class, fn() => new ResponseManager(
            $instance->get(Container::class)
        ));

        // Router
        $instance->set(Router::class, fn() => (new RouterFactory())->make(
            $instance->get(Container::class)
        ));

        // Database adapter
        $instance->set(DatabaseAdapter::class, function () {

            $config_db = Config::get('database');

            $credentials = new DatabaseCredentials(
                $config_db['host'],
                $config_db['username'],
                $config_db['password'],
                $config_db['database'],
                $config_db['driver']
            );

            return match (strtolower($config_db['driver'])) {
                'mysql' => new MysqlAdapter($credentials),
//                'pgsql' => new PostgresAdapter($credentials),
                default => throw new LumioDatabaseException('Database driver "' . $config_db['driver'] . '" not supported'),
            };
        });

        // Model
        $instance->set(ModelFactory::class, fn() => new ModelFactory(
            $instance->get(DatabaseAdapter::class)
        ));

        // View
        $instance->set(ViewFactory::class, fn() => new ViewFactory());

        // Controller
        $instance->set(ControllerFactory::class, fn() => new ControllerFactory($instance));

        // Security - CSRF
        $instance->set(CSRF::class, fn() => (new CSRFFactory($instance))->make());

        // Breadcrumb
        $instance->set(BreadcrumbBuilder::class, fn() => new BreadcrumbBuilder());

        // Database logger
        $instance->set(DatabaseLogger::class, fn() => new DatabaseLogger($instance));
        $instance->set(DatabaseLoggerProxy::class, fn() => new DatabaseLoggerProxy($instance));



        return $instance;
    }

}
