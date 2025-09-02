<?php

namespace Lumio\Factory;

use Lumio\Database\DatabaseAdapter;
use Lumio\Model\BaseModel;

class ModelFactory {

    /**
     * instance of database adapter
     *
     * @author TB
     * @date 7.5.2025
     *
     * @var DatabaseAdapter
     */
    private DatabaseAdapter $_adapter;

    /**
     * Factory for creating models
     *
     * @author TB
     * @date 7.5.2025
     *
     * @param DatabaseAdapter $adapter
     *
     * @return void
     */
    public function __construct(DatabaseAdapter $adapter) {
        $this->_adapter = $adapter;
    }

    /**
     * Make a model
     *
     * @author TB
     * @date 7.5.2025
     *
     * @param string $name
     *
     * @return BaseModel
     *
     * @throws \Exception
     */
    public function make(string $name): BaseModel {

        $model_class = "App\\Models\\" . ucfirst($name) . 'Model';

        if (!class_exists($model_class)) {
            throw new \Exception('Model "' . $name . '" not found');
        }

        return new $model_class($this->_adapter);
    }

}
