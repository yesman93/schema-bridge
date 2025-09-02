<?php

namespace Lumio;

use Exception;
use http\Exception\InvalidArgumentException;
use Lumio\Commander\JSConstantGenerator;
use Lumio\Commander\Template;
use Lumio\Config;
use Lumio\Container;
use Lumio\Database\DatabaseAdapter;
use Lumio\Database\Query;
use Lumio\Database\Scaffoldr\Scaffoldr;
use function Lumio\get_plural;

class Commander {

    /**
     * CLI arguments
     *
     * @author TB
     * @date 8.6.2025
     *
     * @var array
     */
    private array $_args;

    /**
     * Container instance
     *
     * @author TB
     * @date 18.6.2025
     *
     * @var Container
     */
    private Container $_container;

    /**
     * Commander - tool for handling CLI commands in Lumio Forge
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param array $args
     *
     * @return void
     */
    public function __construct(array $args) {
        $this->_args = $args;
        $this->_container = Container::setup();
    }

    /**
     * Handle CLI command
     *
     * @author TB
     * @date 8.6.2025
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle(): void {

        if (!isset($this->_args[1])) {
            $this->_print_help();
            return;
        }

        $command = $this->_args[1];
        $params = array_slice($this->_args, 2);

        match ($command) {

            'make:model' => $this->_make_model($params),
            'make:controller' => $this->_make_controller($params),
            'make:view:outline' => $this->_make_view_outline($params[0] ?? ''),
            'make:view:addedit' => $this->_make_view_addedit($params[0] ?? ''),
            'make:mvc' => $this->_make_mvc($params),
            'make:middleware', 'make:middle' => $this->_make_middleware($params),

            'seed:run' => $this->_run_seeders($params),
            'seed:make' => $this->_make_seeder($params[0] ?? ''),

            'database:sync', 'db:sync' => $this->_sync_scaffoldr(),

            'build:js-enums' => $this->_generate_js_enums($params),

            'list:commands', 'list:cmd' => $this->_list_commands(),
            '--help', '-h' => $this->_print_help(),

            default => $this->_line('Unknown command, please use "forge list" or "forge --help" to see available commands.'),
        };
    }

    /**
     * Print help into the console
     *
     * @author TB
     * @date 8.6.2025
     *
     * @return void
     */
    private function _print_help(): void {

        $this->_line(" ");
        $this->_line("Lumio Forge CLI");
        $this->_line("---------------");
        $this->_line(" ");
        $this->_list_commands();
        $this->_line(" ");
        $this->_info("Usage:");
        $this->_line("  forge make:model user");
        $this->_line("  forge make:controller user [--bare]");
        $this->_line("  forge make:view:outline user");
        $this->_line("  forge make:view:addedit user");
        $this->_line("  forge make:mvc user [--noaddedit] [--nooutline] [--noview]");
        $this->_line("  forge make:middle user");
        $this->_line("  forge seed:run user role");
        $this->_line("  forge seed:run all");
        $this->_line("  forge seed:make user");
        $this->_line("  forge database:sync|db:sync");
        $this->_line("  forge build:js-enums [--log]");
        $this->_line("  forge list:commands|list:cmd");
        $this->_line(" ");
    }

    /**
     * List available commands into the console
     *
     * @author TB
     * @date 8.6.2025
     *
     * @return void
     */
    private function _list_commands(): void {

        $this->_info("Available commands:");
        $this->_line("  make:model <name>                             Create a new model class");
        $this->_line("  make:controller <name> [--flags]              Create a new controller class");
        $this->_line("                                                  Flags: --bare");
        $this->_line("  make:view:outline <name>                      Create an outline view for the given MVC");
        $this->_line("  make:view:addedit <name>                      Create an add/edit form view for the given MVC");
        $this->_line("  make:mvc <name> [--flags]                     Create full MVC from YAML schema");
        $this->_line("                                                  Flags: --noaddedit, --nooutline, --noview");
        $this->_line("  make:middleware|make:middle <name>            Create a new middleware class");
        $this->_line("  seed:run <seeder> [<seeder2> ...]             Run one or more seeders");
        $this->_line("  seed:run all                                  Run all available seeders");
        $this->_line("  seed:make <name> [<name2> ...]                Create one or more seeders");
        $this->_line("  database:sync|db:sync                         Synchronize database schema with YAML definitions");
        $this->_line("                                                  - adds new tables and columns, alters them, but does not delete anything");
        $this->_line("  build:js-enums [--flags]                      Generate JavaScript constants from PHP constants");
        $this->_line("                                                  Flags: --log (logs constants in browser console)");
        $this->_line("  list:commands|list:cmd                        List all available commands");
    }

    /**
     * Print a line
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param string $message
     *
     * @return void
     */
    private function _line(string $message): void {
        echo "{$message}\n";
    }

    /**
     * Print error message in red
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param string $message
     *
     * @return void
     */
    private function _error(string $message): void {
        $this->_line("\033[31m{$message}\033[0m");
    }

    /**
     * Print success message in green
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param string $message
     *
     * @return void
     */
    private function _success(string $message): void {
        $this->_line("\033[32m{$message}\033[0m");
    }

    /**
     * Print info message in blue
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param string $message
     *
     * @return void
     */
    private function _info(string $message): void {
        $this->_line("\033[34m{$message}\033[0m");
    }

    /**
     * Print warning message in yellow
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param string $message
     *
     * @return void
     */
    private function _warning(string $message): void {
        $this->_line("\033[33m{$message}\033[0m");
    }

    /**
     * Create a seeder
     *
     * @author TB
     * @date 30.6.2025
     *
     * @param string $name
     *
     * @return void
     *
     * @throws Exception
     */
    private function _make_seeder(string $name): void {

        if ($name === '') {
            $this->_error('Seeder name not given!');
            return;
        }

        $dir = ROOT_PATH . 'src/Database/Seeders';
        $class_name = ucfirst($name) . 'Seeder';
        $file_name = $class_name . '.php';

        $path = str_replace('\\', '/', $dir) . '/' . $file_name;

        if (file_exists($path)) {

            $this->_warning("Seeder already exists: {$path}");

            $confirm = $this->_confirm("Overwrite? (y/N)", false);
            if (!$confirm) {
                $this->_info("Seeder creation cancelled");
                return;
            }

            @unlink($path);
        }

        $plural = get_plural(strtolower($name));
        $date = date('j.n.Y');

        $query = (new Query())->select()->table($name)->build();
        $db = $this->_container->get(DatabaseAdapter::class);
        $rows = $db->all(...$query);
        if ($rows === []) {
            $this->_error("No data found in table '{$name}'!");
            $this->_warning("Seeder creation cancelled");
        } else {
            $this->_info("Found " . count($rows) . " rows in table '{$name}'");
        }

        $data = var_export($rows, true);

        $template = Template::seeder($class_name, $date, $plural, $name, $data);

        touch($path);

        $save = file_put_contents($path, $template);
        if ($save === false) {
            $this->_error("Could not create seeder file: {$path}");
            return;
        }

        exec("git add {$path}");

        $this->_success("Seeder {$class_name} created successfully at {$path}");
    }

    /**
     * Create a model
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param array $params
     *
     * @return void
     */
    private function _make_model(array $params): void {

        if (empty($params[0])) {
            $this->_error('Model name not given!');
            return;
        }

        $name = $name_raw = $params[0];
        $name = ucfirst($name) . 'Model';
        $dir = realpath(ROOT_PATH . 'app/Models');
        $path = str_replace('\\', '/', $dir) . '/' . $name . '.php';

        if (file_exists($path)) {
            $this->_error('Model ' . $path . ' already exists!');
            return;
        }

        $template = Template::model($name, get_plural(strtolower($name_raw)), date('j.n.Y'));

        touch($path);

        $save = file_put_contents($path, $template);
        if ($save === false) {
            $this->_error('Could not create model file: ' . $path);
            return;
        }

        exec("git add {$path}");

        $this->_success('Model ' . $name . ' created successfully at ' . $path);
    }

    /**
     * Create a controller
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param array $params
     *
     * @return void
     */
    private function _make_controller(array $params): void {

        if (empty($params[0])) {
            $this->_error('Controller name not given!');
            return;
        }

        $name = $name_raw = strtolower($params[0]);
        if (strpos($name, '/') !== false) {

            if (substr_count($name, '/') > 1) {
                $this->_error('Invalid name format!');
                $this->_warning('Expected "realm/name" or "name"');
                return;
            }

            [$realm, $controller] = explode('/', $name, 2);
            $namespace = '\\' . ucfirst($realm);
            $class_name = ucfirst($controller) . 'Controller';
            $file_name = $realm . '/' . ucfirst($controller) . 'Controller';
            $name_raw = $controller;

        } else {
            $namespace = '';
            $class_name = ucfirst($name) . 'Controller';
            $file_name = $class_name;
            $name_raw = strtolower($name_raw);
        }

        $dir = realpath(ROOT_PATH . 'app/Controllers');
        $path = str_replace('\\', '/', $dir) . '/' . $file_name . '.php';

        if (file_exists($path)) {
            $this->_error('Controller ' . $path . ' already exists!');
            return;
        }

        $date = date('j.n.Y');
        $plural = get_plural($name_raw);
        $plural_readable = ucfirst($plural);

        $is_bare = in_array('--bare', $params);

        if ($is_bare) {
            $template_methods = '';
        } else {

            $template_ignite = Template::controller_ignite($name, $name_raw, $plural, $plural_readable, $date);
            $template_outline = Template::controller_outline($plural, $date);
            $template_prepare_addedit = Template::controller_prepare_addedit($date);
            $template_add = Template::controller_add($name_raw, $plural, $date);
            $template_edit = Template::controller_edit($name_raw, $plural, $date);
            $template_delete = Template::controller_delete($name_raw, $plural, $date);

            $template_methods = $template_ignite . $template_outline . $template_prepare_addedit . $template_add . $template_edit . $template_delete;
        }

        $template = Template::controller($class_name, $namespace, $plural, $date, $template_methods);

        touch($path);

        $save = file_put_contents($path, $template);
        if ($save === false) {
            $this->_error('Could not create controller file: ' . $path);
            return;
        }

        exec("git add {$path}");

        $this->_success('Controller ' . $class_name . ' created successfully at ' . $path);
    }

    /**
     * Create a view for outline of given MVC
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param string $mvc
     *
     * @return void
     *
     * @throws Exception
     */
    private function _make_view_outline(string $mvc): void {

        if (empty($mvc)) {
            $this->_error('MVC name not given!');
            return;
        }

        $realm = '';
        $name = $mvc;

        // Detect realm and extract name
        if (strpos($mvc, '/') !== false) {

            if (substr_count($mvc, '/') > 1) {
                $this->_error('Invalid MVC format!');
                $this->_warning('Expected "realm/name" or "name"');
                return;
            }

            [$realm, $name] = explode('/', $mvc, 2);
        }

        $plural = get_plural($name);
        $schema_dir = Config::get('scaffoldr.schema_path');
        $schema_path = str_replace('\\', '/', $schema_dir) . '/' . $name . '.yaml';

        if (!file_exists($schema_path)) {
            $this->_error("Schema file not found at {$schema_path}");
            return;
        }

        $schema = spyc_load_file($schema_path);
        $columns = $schema['columns'] ?? [];

        if (empty($columns)) {
            $this->_error("No columns found in schema at {$schema_path}");
            return;
        }

        $view_base = Config::get('app.view.path_pages');
        $dir = $realm ? "{$view_base}/{$realm}/{$name}" : "{$view_base}/{$name}";
        $file = str_replace('\\', '/', $dir) . '/' . $plural . '.php';

        if (file_exists($file)) {
            $this->_error("Outline view already exists at {$file}");
            return;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Build column definitions
        $cols_code = '';
        $pk = '';
        foreach ($columns as $col) {

            $column_name = $col['name'] ?? '';
            if ($column_name === '') {
                continue;
            }

            $label = ucfirst(str_replace('_', ' ', $column_name));
            $width = $col['width'] ?? 160;

            $type = strtolower($col['type'] ?? '');
            $class = 'Text';

            if (in_array($type, ['int', 'smallint', 'bigint'])) {
                $class = 'Number';
            } elseif ($type === 'tinyint' && ($col['length'] ?? null) == 1) {
                $class = 'Boolean';
            } elseif (in_array($type, ['decimal', 'numeric', 'float'])) {
                $class = 'Currency';
            } elseif ($type === 'date') {
                $class = 'Date';
            } elseif (in_array($type, ['datetime', 'timestamp'])) {
                $class = 'Datetime';
            } elseif ($type === 'time') {
                $class = 'Time';
            } elseif (str_contains($column_name, 'url') || str_contains($column_name, 'link')) {
                $class = 'Link';
            } elseif (str_contains($type, 'blob') || str_contains($column_name, 'image')) {
                $class = 'Image';
            }

            if ($col['primary'] ?? false) {
                $pk = $column_name;
                $label = 'ID';
            }

            $cols_code .= "    ->add_column((new Column\\{$class}('{$column_name}', __tx('{$label}'), {$width})))\n";
        }

        $var_name = '$this->' . $plural;

        $template = Template::outline($mvc, $pk, $name, $var_name, $cols_code);

        $save = file_put_contents($file, $template);
        if ($save === false) {
            $this->_error("Could not create outline view at {$file}");
            return;
        }

        exec("git add {$file}");

        $this->_success("Outline view created at {$file}");
    }

    /**
     * Create a view for add/edit form of given MVC
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param string $mvc
     *
     * @return void
     *
     * @throws Exception
     */
    private function _make_view_addedit(string $mvc): void {

        if (empty($mvc)) {
            $this->_error('MVC name not given!');
            return;
        }

        $realm = '';
        $name = $mvc;

        if (strpos($mvc, '/') !== false) {

            if (substr_count($mvc, '/') > 1) {
                $this->_error('Invalid MVC format!');
                $this->_warning('Expected "realm/name" or "name"');
                return;
            }

            [$realm, $name] = explode('/', $mvc, 2);
        }

        $schema_dir = Config::get('scaffoldr.schema_path');
        $schema_path = str_replace('\\', '/', $schema_dir) . '/' . $name . '.yaml';

        if (!file_exists($schema_path)) {
            $this->_error("Schema file not found at {$schema_path}");
            return;
        }

        $schema = spyc_load_file($schema_path);
        $columns = $schema['columns'] ?? [];

        if (empty($columns)) {
            $this->_error("No columns found in schema at {$schema_path}");
            return;
        }

        $view_base = Config::get('app.view.path_pages');
        $dir = $realm ? "{$view_base}/{$realm}/{$name}" : "{$view_base}/{$name}";
        $dir = str_replace('\\', '/', $dir);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $date = date('j.n.Y');

        // Create add.php and edit.php
        foreach (['add', 'edit'] as $type) {

            $wrapper = "{$dir}/{$type}.php";

            if (!file_exists($wrapper)) {

                $submit = $type === 'add' ? 'Add' : 'Save';

                $data = $data_use = '';
                if ($type === 'edit') {

                    $data_use = <<<PHP
use Lumio\\DTO\\View\\FormSetup;
PHP;

                    $data = <<<PHP
new FormSetup(
    data: \$this->{$mvc},
    data_id: '{$mvc}_id',
)
PHP;
                }

                $wrapper_code = Template::addedit_wrapper($name, $type, $date, $data_use, $data, $submit);

                touch($wrapper);
                $save_wrapper = file_put_contents($wrapper, $wrapper_code);
                if ($save_wrapper === false) {
                    $this->_error("Could not create view wrapper at {$wrapper}");
                    return;
                }

                exec("git add {$wrapper}");

                $this->_success("View wrapper {$type} created: {$wrapper}");
            }
        }

        // Build FormInput components
        $fields_code = '';
        foreach ($columns as $col) {

            $column_name = $col['name'] ?? '';
            $type = strtolower($col['type'] ?? '');

            if ($col['primary'] ?? false || $column_name === '') {
                continue;
            }

            $required = $col['nullable'] ?? false ? 'false' : 'true';

            $label = ucfirst(str_replace('_', ' ', $column_name));

            $input = <<<PHP
new FormInput(
    name: '{$column_name}', 
    label: __tx('{$label}'),
    required: {$required},
) 
PHP;

            $input = trim($input);

            switch (true) {
                case in_array($type, ['text', 'varchar', 'char']) && strpos($column_name, 'password') !== false:
                    $map = "password({$input})";
                    break;
                case $type == 'date':
                    $map = "date({$input})";
                    break;
                case $type == 'time':
                    $map = "time({$input})";
                    break;
                case strpos($column_name, 'color') !== false:
                    $map = "color({$input})";
                    break;
                case strpos($column_name, 'file') !== false
                || strpos($column_name, 'fpath') !== false
                || strpos($column_name, 'filepath') !== false:
                    $map = "file({$input})";
                    break;
                case in_array($type, ['text', 'mediumtext', 'longtext']):
                    $map = "textarea({$input})";
                    break;
                default:
                    $map = "text({$input})";
            };

            $field_code = <<<PHP
\$form->{$map};


PHP;
            $fields_code .= $field_code;
        }

        $template = Template::addedit_form($name, $date, $fields_code);

        $addedit_dir = "{$dir}/addedit";
        if (!is_dir($addedit_dir)) {
            mkdir($addedit_dir, 0755, true);
        }

        $path = "{$addedit_dir}/addedit.inc.php";

        if (file_exists($path)) {
            $this->_error("addedit.inc.php already exists at {$path}");
            return;
        }

        touch($path);
        $save = file_put_contents($path, $template);
        if ($save === false) {
            $this->_error("Could not create addedit.inc.php at {$path}");
            return;
        }

        exec("git add {$path}");

        $this->_success("addedit.inc.php created at {$path}");
    }

    /**
     * Create full MVC stack (model, controller, views)
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param array $params
     *
     * @return void
     *
     * @throws Exception
     */
    private function _make_mvc(array $params): void {

        if (empty($params[0])) {
            $this->_error('MVC name not given!');
            return;
        }

        $mvc = $params[0];
        $flags = array_slice($params, 1);

        $no_outline = in_array('--nooutline', $flags);
        $no_addedit = in_array('--noaddedit', $flags);
        $no_view = in_array('--noview', $flags);

        // Always try creating model
        $this->_make_model([$mvc]);

        // Then the controller
        $this->_make_controller([$mvc]);

        // Then views unless -noview is present
        if (!$no_view) {

            if (!$no_outline) {
                $this->_make_view_outline($mvc);
            } else {
                $this->_info('Outline view skipped');
            }

            if (!$no_addedit) {
                $this->_make_view_addedit($mvc);
            } else {
                $this->_info('Add/edit view skipped');
            }

        } else {
            $this->_info('Views skipped');
        }
    }

    /**
     * Create a middleware
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param array $params
     *
     * @return void
     */
    private function _make_middleware(array $params): void {

        if (empty($params[0])) {
            $this->_error('Middleware name not given!');
            return;
        }

        // snake_case → PascalCase + "Middleware"
        $raw  = $params[0];
        $parts = explode('_', $raw);
        $class = implode('', array_map('ucfirst', $parts)) . 'Middleware';

        $dir = realpath(ROOT_PATH . 'src/Middleware');
        $path = str_replace('\\', '/', $dir) . '/' . $class . '.php';

        if (file_exists($path)) {
            $this->_error("Middleware already exists: {$path}");
            return;
        }

        // Ensure directory
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $date = date('j.n.Y');

        $template = Template::middleware($class, $date);

        if (file_put_contents($path, $template) === false) {
            $this->_error("Could not create middleware file: {$path}");
            return;
        }

        exec("git add {$path}");

        $this->_success("Middleware {$class} created at {$path}");
    }

    private function _sync_scaffoldr(): void {

        $dir = Config::get('scaffoldr.schema_path');
        $files = glob($dir . '/*.yaml');

        $this->_warning("You are about to sync the following database tables:");
        foreach ($files as $file) {
            $this->_info(" - " . basename($file, '.yaml'));
        }

        $confirm = $this->_confirm("Continue? (y/N)", false);
        if (!$confirm) {
            $this->_info("Database schema synchronization cancelled");
            return;
        }

        try {
            $container = $this->_container;
            (new Scaffoldr($container))->sync();
        } catch (Exception $e) {
            $this->_error("Error during database schema synchronization: " . $e->getMessage());
            return;
        }

    }

    /**
     * Run specified seeders
     *
     * @author TB
     * @date 18.6.2025
     *
     * @param array $params
     *
     * @return void
     *
     * @throws Exception
     */
    private function _run_seeders(array $params): void {

        if (empty($params)) {
            $this->_error('No seeders specified!');
            return;
        }

        $namespace = 'Lumio\\Database\\Seeders\\';
        $seeder_classes = [];
        $seeders = [];

        if (count($params) === 1 && $params[0] === 'all') {

            $seeders_path = Config::get('seeder.path_classes');
            if (!is_dir($seeders_path)) {
                $this->_error("Seeders directory not found: $seeders_path");
                return;
            }

            $files = scandir($seeders_path);
            foreach ($files as $file) {

                if (
                    is_file($seeders_path . '/' . $file) &&
                    substr($file, -10) === 'Seeder.php' &&
                    $file !== 'Seeder.php'
                ) {

                    $class_name = substr($file, 0, -4); // remove ".php"
                    $fqcn = $namespace . $class_name;
                    if (class_exists($fqcn)) {
                        $seeder_classes[] = $fqcn;
                        $seeders[$fqcn] = strtolower(substr($class_name, 0, -6)); // Remove "Seeder"
                    }
                }
            }

        } else {

            foreach ($params as $name) {
                $class = $namespace . ucfirst($name) . 'Seeder';
                $seeder_classes[] = $class;
                $seeders[$class] = strtolower($name);
            }
        }

        $db = $this->_container->get(DatabaseAdapter::class);
        $db_tables = $db->get_tables();

        $errors = [];

        $tables = array_filter($seeders, function($table, $class) use ($db_tables, &$errors) {

            $plural = get_plural($table);

            if (!class_exists($class)) {
                $errors[] = "Seeder class \"{$class}\" does not exist! Seeding $plural will be skipped";
                return false;
            }

            if (!isset($db_tables[$table])) {
                $errors[] = "Table \"{$table}\" does not exist in the database! Seeding $plural will be skipped";
                return false;
            }

            return true;

        }, ARRAY_FILTER_USE_BOTH);

        $this->_line('');
        $this->_info("You are about to run the following seeders:");
        foreach ($seeder_classes as $class) {
            $this->_line(" - " . $class);
        }

        if (!empty($tables)) {
            $this->_line('');
            $this->_warning("This will TRUNCATE and OVERWRITE the following tables:");
            foreach ($tables as $table) {
                $this->_line(" - " . $table);
            }
        }

        if ($errors !== []) foreach ($errors as $e) {
            $this->_warning($e);
        }

        $this->_line('');
        $confirm = $this->_confirm("Continue? (y/N)", false);
        if (!$confirm) {
            $this->_info("Seeder execution cancelled");
            return;
        }

        $this->_line('');

        foreach ($seeders as $class => $table) {

            if (!class_exists($class)) {
                continue;
            }

            if (!isset($db_tables[$table])) {
                continue;
            }

            $seeder = new $class($db);

            $plural = get_plural($table);
            if (!method_exists($seeder, 'run')) {
                $this->_error("Class $class has no run() method! Seeding {$plural} will be skipped");
                continue;
            }

            $this->_info("Starting to seed $plural ...");

            try {
                $seeder->run();
                $this->_success("Seeding $plural completed");
            } catch (\Throwable $e) {
                $this->_error('Error seeding ' . $plural . ': ' . $e->getMessage());
                $this->_error('Last query: ' . $seeder->get_last_sql());
                $this->_error('Last params: ' . print_r($seeder->get_last_params(), true));
            }

            $this->_line('');
        }
    }

    /**
     * Prints a confirmation question and waits for user input
     *
     * @author TB
     * @date 18.6.2025
     *
     * @param string $question
     * @param bool $default_yes
     *
     * @return bool
     */
    private function _confirm(string $question, bool $default_yes = true): bool {

        $default = $default_yes ? 'Y/n' : 'y/N';
        $this->_line($question . " [$default] ", false);
        $handle = fopen("php://stdin", "r");
        $line = strtolower(trim(fgets($handle)));
        fclose($handle);

        if ($line === '') {
            return $default_yes;
        }

        return in_array($line, ['y', 'yes'], true);
    }

    /**
     * Generate JavaScript enums file from PHP constants
     *
     * @author TB
     * @date 8.6.2025
     *
     * @param array $params
     *
     * @return void
     *
     * @throws Exception
     */
    private function _generate_js_enums(array $params): void {

        $path = Config::get('app.view.path_assets') . '/js/enums.js';

        $generated = (new JSConstantGenerator())->generate($path, in_array('--log', $params));
        if (!$generated) {
            $this->_error('Could not generate JavaScript enums file at ' . $path);
            return;
        }

        exec("git add {$path}");

        $this->_success('JavaScript enums file generated successfully at ' . $path);
    }

}
