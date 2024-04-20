<?php

include('../vendor/autoload.php');

$migrations = new \Nimblephp\migrations\Migrations(__DIR__);
$migrations->runMigrations();