<?php

namespace App\Models;

use Lumio\Database\DatabaseAdapter;
use Lumio\Model\BaseModel;

class PageModel extends BaseModel {

    public function __construct(DatabaseAdapter $db) {

        parent::__construct($db);
    }

}