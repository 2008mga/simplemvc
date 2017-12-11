<?php
/*
 * PLACE YOU ROUTES HERE
 * */
$route->group('/api', function ($route) {
    $route->get('/', "\Simple\Controllers\IndexController::index");
    $route->get('/by/year', "\Simple\Controllers\IndexController::byYear");
    $route->get('/by/month', "\Simple\Controllers\IndexController::byMonth");
    $route->get('/by/hour', '\Simple\Controllers\IndexController::byHour');
    $route->get('/by/day', '\Simple\Controllers\IndexController::byDay');
    $route->get('/insert', '\Simple\Controllers\IndexController::insert');
});

$route->get('/', '\Simple\Controllers\PageController::index');
/*
 * END PLACE ROUTES
 * */

