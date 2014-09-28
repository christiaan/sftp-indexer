<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$app = new \Christiaan\SftpIndexer\Cli\Application(__DIR__ . '/config.yaml');

$app->run();