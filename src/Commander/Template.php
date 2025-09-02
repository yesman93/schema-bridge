<?php

namespace Lumio\Commander;

class Template {

    /**
     * Get template for a model with the given name
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $name
     * @param string $plural
     * @param string $date
     *
     * @return string
     */
    public static function model(string $name, string $plural, string $date): string {

        return <<<PHP
<?php

namespace App\Models;

use Exception;
use Lumio\Database\DatabaseAdapter;
use Lumio\Model\BaseModel;

class {$name} extends BaseModel {

    /**
     * Model for {$plural}
     *
     * @author TB
     * @date {$date}
     *
     * @param DatabaseAdapter \$db
     *
     * @return void
     * 
     * @throws Exception
     */
    public function __construct(DatabaseAdapter \$db) {

        parent::__construct(\$db);
    }

}

PHP;
    }

    /**
     * Get template for outline with given parameters
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $mvc
     * @param string $pk
     * @param string $name
     * @param string $var_name
     * @param string $cols_code
     *
     * @return string
     */
    public static function outline(string $mvc, string $pk, string $name, string $var_name, string $cols_code): string {

        return <<<PHP
<?php

use Lumio\View\Components;
use Lumio\View\Components\Listview;
use Lumio\View\Components\Listview\Column;

\$listview = Components\ListView::build({$var_name})
{$cols_code};

\$listview->actions(
    new Listview\Action('/{$mvc}/view', '{$pk}', __tx('Show'), 'far fa-search'),
    new Listview\Action('/{$mvc}/edit', '{$pk}', __tx('Edit'), 'far fa-pen', true),
    (new Listview\Action('/{$mvc}/delete', '{$pk}', __tx('Delete'), 'far fa-trash', false, null, true))->confirm(__tx('Are you sure you want to delete this item?')),
);

\$listview->button(Components\Button::build(
    link: '/{$mvc}/add',
    label: __tx('Add {$name}'),
    icon: 'far fa-plus',
    is_modal: true,
    class: 'btn btn-success',
));

\$listview->render();

PHP;
    }

    /**
     * Get template for add/edit form with given parameters
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $name
     * @param string $date
     * @param string $fields_code
     *
     * @return string
     */
    public static function addedit_form(string $name, string $date, string $fields_code): string {

        return <<<PHP
<?php
/**
 * {$name} add/edit form
 * 
 * @author TB
 * @date {$date}
 * 
 */

use Lumio\\DTO\\View\\FormInput;



{$fields_code}

PHP;
    }

    /**
     * Get template for add/edit wrapper with given parameters
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $name
     * @param string $type
     * @param string $date
     * @param string $data_use
     * @param string $form_setup
     * @param string $submit_label
     *
     * @return string
     */
    public static function addedit_wrapper(
        string $name,
        string $type,
        string $date,
        string $data_use,
        string $form_setup,
        string $submit_label
    ): string {

        return <<<PHP
<?php
/**
 * {$name} {$type} 
 * 
 * @author TB
 * @date {$date}
 * 
 */

use Lumio\\View\\Components\\Form;
use Lumio\\DTO\\View\\FormInput;  
{$data_use}



\$form = Form::build({$form_setup});

include 'addedit/addedit.inc.php';

\$form->submit(new FormInput(
    label: __tx('{$submit_label}'),
));

PHP;
    }

    /**
     * Get template for a controller with given parameters
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $class_name
     * @param string $namespace
     * @param string $plural
     * @param string $date
     * @param string $methods
     *
     * @return string
     */
    public static function controller(string $class_name, string $namespace, string $plural, string $date, string $methods): string {

        return <<<PHP
<?php

namespace App\Controllers{$namespace};

use Exception;
use Throwable;
use Lumio\Controller\BaseController;
use Lumio\DTO\View\BreadcrumbItem;
use Lumio\Exceptions\LumioDatabaseException;
use Lumio\Exceptions\LumioValidationException;
use Lumio\IO\MessageBag;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Log\Logger;
use Lumio\Model\BaseModel;
use Lumio\View\View;

class {$class_name} extends BaseController {

    /**
     * Controller for {$plural}
     *
     * @author TB
     * @date {$date}
     *
     * @param BaseModel|null \$model
     * @param Request \$request
     * @param Response \$response
     * @param View \$view
     *
     * @return void
     */
    public function __construct(?BaseModel \$model, Request \$request, Response \$response, View \$view) {
        parent::__construct(\$model, \$request, \$response, \$view);
    }
    {$methods}
}

PHP;
    }

    /**
     * Get template for controller ignite hook
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $name
     * @param string $name_raw
     * @param string $plural
     * @param string $plural_readable
     * @param string $date
     *
     * @return string
     */
    public static function controller_ignite(string $name, string $name_raw, string $plural, string $plural_readable, string $date): string {

        return <<<PHP

    /**
     * Initial hook of the controller
     * 
     * @author TB
     * @date {$date}
     * 
     * @return void
     * 
     * @throws Exception
     */
    public function ignite(): void {
        \$this->breadcrumb(new BreadcrumbItem('/{$name_raw}/{$plural}', __tx('{$plural_readable}')));
    }

PHP;

    }

    /**
     * Get template for controller prepare add/edit method
     *
     * @author TB
     * @date 18.6.2025
     *
     * @param string $date
     *
     * @return string
     */
    public static function controller_prepare_addedit(string $date): string {

        return <<<PHP
    
    /**
     * Prepare add/edit form
     * 
     * @author TB
     * @date {$date}
     * 
     * @return void
     */
    private function _prepare_addedit(): void {
    
        // TODO: put retrieving choices and other data for form here
    }

PHP;

    }

    /**
     * Get template for controller outline method
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $plural
     * @param string $date
     *
     * @return string
     */
    public static function controller_outline(string $plural, string $date): string {

        return <<<PHP

    /**
     * Outline of {$plural}
     * 
     * @author TB
     * @date {$date}
     * 
     * @return mixed
     * 
     * @throws Exception
     */
    public function {$plural}(): mixed {
    
        parent::list();
        
        \$this->master(View::MASTER_PRIVATE);
    
        \${$plural} = \$this->_model->all();
        \$this->assign('{$plural}', \${$plural});
        
        return null;
    }

PHP;

    }

    /**
     * Get template for controller add method
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $name
     * @param string $plural
     * @param string $date
     *
     * @return string
     */
    public static function controller_add(string $name, string $plural, string $date): string {

        $name_start = ucfirst($name);

        return <<<PHP

    /**
     * Add {$name}
     * 
     * @author TB
     * @date {$date}
     * 
     * @return mixed
     * 
     * @throws Exception
     */
    public function add(): mixed {
    
        parent::add(); 

        \$this->master(View::MASTER_MODAL);
        
        \$this->_prepare_addedit();
        
        if (!\$this->is_submit()) {
            return false;
        }

        if (!\$this->is_valid()) {
            return false;
        }
        
        try {
        
            \${$name}_id = \$this->_model->add();
            if (\${$name}_id > 0) {
                \$this->log()->info('Added {$name} with ID: ' . \${$name}_id);
                return \$this->close_modal('/{$name}/{$plural}')->success(__tx('%s successfully added!$', __tx('{$name_start}')));
            }
            
            MessageBag::error(__tx('%s could not be added!', __tx('{$name_start}')));
            
            return false;
        
        } catch (LumioValidationException \$e) {
            MessageBag::error(\$e->getMessage());
            return false;
        } catch (LumioDatabaseException \$e) {
            \$code = \$e->get_code();
            Logger::channel('db')->error('Error adding {$name}, code: ' . \$code . ', message: ' . \$e->getMessage());
            MessageBag::error(__tx('Failed to add %s, contact technical support with code %s', __tx('{$name}'), \$code));
            return false;
        } catch (Throwable \$e) {
            Logger::channel('error')->error('Error adding {$name}: ' . \$e->getMessage());
            MessageBag::error(__tx('An unexpected error occurred while adding %s, contact technical support', __tx('{$name}')));
            return false;
        }
    }

PHP;
    }

    /**
     * Get template for controller edit method
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $name
     * @param string $plural
     * @param string $date
     *
     * @return string
     */
    public static function controller_edit(string $name, string $plural, string $date): string {

        $name_start = ucfirst($name);

        return <<<PHP

    /**
     * Edit {$name}
     *
     * @author TB
     * @date {$date}
     *
     * @param mixed \$id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function edit(mixed \$id = null): mixed {

        \$destination = \$this->close_modal(true);

        try {
            \${$name} = \$this->_model->get(\$id);
        } catch (Throwable \$e) {
            \${$name} = [];
        }

        if (\${$name} === []) {
            return \$destination->error(__tx('%s not found!', __tx('{$name_start}')));
        }

        \$this->assign('{$name}', \${$name});

        parent::edit(\$id);

        \$this->master(View::MASTER_MODAL);
        
        \$this->_prepare_addedit();

        if (!\$this->is_submit()) {
            return false;
        }

        if (!\$this->is_valid()) {
            return false;
        }

        try {

            \${$name}_id = \$this->_model->edit();
            if (\${$name}_id > 0) {
                \$this->log()->info('Modified {$name} with ID: ' . \${$name}_id);
                return \$destination->success(__tx('%s successfully modified!', __tx('{$name_start}')));
            }

            MessageBag::error(__tx('%s could not be modified!', __tx('{$name_start}')));

            return false;

        } catch (LumioValidationException \$e) {
            MessageBag::error(\$e->getMessage());
            return false;
        } catch (LumioDatabaseException \$e) {
            \$code = \$e->get_code();
            Logger::channel('db')->error('Error modifying {$name}, code: ' . \$code . ', message: ' . \$e->getMessage());
            MessageBag::error(__tx('Failed to modify %s, contact technical support with code %s', __tx('{$name}'), \$code));
            return false;
        } catch (Throwable \$e) {
            Logger::channel('error')->error('Error modifying {$name}: ' . \$e->getMessage());
            MessageBag::error(__tx('An unexpected error occurred while modifying %s, contact technical support', __tx('{$name}')));
            return false;
        }
    }

PHP;
    }

    /**
     * Get template for controller delete method
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $name
     * @param string $plural
     * @param string $date
     *
     * @return string
     */
    public static function controller_delete(string $name, string $plural, string $date): string {

        $name_start = ucfirst($name);

        return <<<PHP

    /**
     * Delete {$name}
     *
     * @author TB
     * @date {$date}
     *
     * @param mixed \$id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function delete(mixed \$id = null): mixed {

        \$destination = \$this->redirect('/{$name}/{$plural}');

        \${$name} = \$this->_model->get(\$id);
        if (\${$name} === []) {
            return \$destination->error(__tx('%s not found!', __tx('{$name_start}')));
        }

        try {

            if (\$this->_model->remove(\$id)) {
                \$this->log()->info(__tx('Deleted {$name} with ID: %s', \$id));
                return \$destination->success(__tx('%s successfully deleted!', __tx('{$name_start}')));
            } else {
                return \$destination->error(__tx('Failed to delete %s!', __tx('{$name}')));
            }

        } catch (LumioValidationException \$e) {
            return \$destination->error(\$e->getMessage());
        } catch (LumioDatabaseException \$e) {
            \$code = \$e->get_code();
            Logger::channel('db')->error('Error deleting setting, code: ' . \$code . ', message: ' . \$e->getMessage());
            return \$destination->error(__tx('Failed to delete %s, contact technical support with code %s', __tx('{$name}'), \$code));
        } catch (Throwable \$e) {
            Logger::channel('error')->error('Error deleting setting: ' . \$e->getMessage());
            return \$destination->error(__tx('An unexpected error occurred while deleting %s, contact technical support', __tx('{$name}')));
        }
    }

PHP;

    }

    /**
     * Get template for middleware
     *
     * @author TB
     * @date 11.6.2025
     *
     * @param string $class
     * @param string $date
     *
     * @return string
     */
    public static function middleware(string $class, string $date): string {

        return <<<PHP
<?php

namespace Lumio\Middleware;

use Lumio\Contract\MiddlewareContract;
use Lumio\Container;
use Lumio\Utilities\Session;

class {$class} implements MiddlewareContract {

    /**
     * Handle the middleware logic
     * 
     * @author TB
     * @date {$date}
     *
     * @param Container \$container
     * @param callable \$next
     *
     * @return mixed
     */
    public function handle(Container \$container, callable \$next): mixed {

        // TODO: put your code here

        return \$next();
    }
}

PHP;

    }

    /**
     * Get template for seeder
     *
     * @author TB
     * @date 27.6.2025
     *
     * @param string $class
     * @param string $date
     * @param string $plural
     * @param string $name
     * @param string $data
     *
     * @return string
     */
    public static function seeder(string $class, string $date, string $plural, string $name, string $data): string {

        return <<<PHP
<?php

namespace Lumio\Database\Seeders;

use Exception;

class {$class} extends Seeder {

    /**
     * Run the seeder to populate table "{$name}" with predefined {$plural}
     *
     * @author TB
     * @date {$date}
     *
     * @return void
     *
     * @throws Exception
     */
    public function run(): void {

        \${$plural} = {$data};

        \$this->truncate('{$name}');
        \$this->insert('{$name}', \${$plural});
    }

}

PHP;

    }

}
