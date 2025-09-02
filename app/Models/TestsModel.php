<?php

namespace App\Models;

use Lumio\Database\DatabaseAdapter;
use Lumio\Model\BaseModel;

class TestsModel extends BaseModel {

    public function __construct(DatabaseAdapter $db) {

        parent::__construct($db);
    }

    public function benchmark_simple() {
        return 'hello model';
    }

}