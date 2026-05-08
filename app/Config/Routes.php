<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('health', function($routes){
    $routes->get('metrics','Front\HealthController::getMetrics');
});
