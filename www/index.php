<?php
/* -------------------------------------------------------------------- */
/* Lumio PHP Framework                                                  */
/*                                                                      */
/*                                                                      */
/* -------------------------------------------------------------------- */




define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);



require ROOT_PATH . 'vendor/autoload.php';



try {

    $kernel = new Lumio\Kernel();
    $kernel->boot();

} catch (Exception $e) {
    \Lumio\Log\Logger::channel('app')->emergency($e->getMessage());
    lumio_fail($e->getMessage(), 500, $e);
}



