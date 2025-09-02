<?php

namespace Lumio\Traits\Database;

trait Engine {

    /**
     * Engine - InnoDB
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    public const ENGINE_INNODB = 'InnoDB';

    /**
     * Engine - MyISAM
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    public const ENGINE_MYISAM = 'MyISAM';

    /**
     * Engine - CSV
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    public const ENGINE_CSV = 'CSV';

    /**
     * Engine - ARIA
     *
     * @author TB
     * @date 26.4.2025
     *
     * @var string
     */
    public const ENGINE_ARIA = 'ARIA'; // For MariaDB

}



