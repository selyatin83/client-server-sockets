<?php
declare(strict_types=1);
set_time_limit(0);

require("vendor/autoload.php");

use Mselyatin\Sockets\infrastructure\Application;

try {
    $app = new Application($argv);
    $app->run();
} catch (\Throwable $e) {
    var_dump($e->getMessage());
}