<?php

namespace Lumio\Traits\Database;

trait Engine {

    /**
     * Engine - InnoDB
     *
     *
     * @var string
     */
    public const ENGINE_INNODB = 'InnoDB';

    /**
     * Engine - MyISAM
     *
     *
     * @var string
     */
    public const ENGINE_MYISAM = 'MyISAM';

    /**
     * Engine - CSV
     *
     *
     * @var string
     */
    public const ENGINE_CSV = 'CSV';

    /**
     * Engine - ARIA
     *
     *
     * @var string
     */
    public const ENGINE_ARIA = 'ARIA'; // For MariaDB

}



