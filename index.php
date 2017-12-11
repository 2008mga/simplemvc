<?php
require_once "vendor/autoload.php";
require_once "app/Kernel/helpers.php";

app()->make('database');

$router = app()->make('router');
$route = $router->collection();
include "router.php";
$router->dispatch();