<?php

require 'vendor/autoload.php';
require __DIR__ . '/sgs.framework/src/Config/bootstrap.php';

use SGS\Core\Application;

$app = new Application();
$app->run();

//Flush the output buffer
if (ob_get_level() > 0) {
    ob_end_flush();
}
