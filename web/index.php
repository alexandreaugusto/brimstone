<?php

date_default_timezone_set("America/Sao_Paulo");

#ini_set('display_errors', 1);

#error_reporting(-1);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

require_once __DIR__.'/../src/app.php';

require_once __DIR__.'/../src/controllers.php';

$app->run();
