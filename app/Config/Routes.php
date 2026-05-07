<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/register/step1', 'RegisterController::step1');
$routes->post('/register/step1', 'RegisterController::postStep1');

$routes->get('/register/step2', 'RegisterController::step2');
$routes->post('/register/step2', 'RegisterController::postStep2');

$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');


$routes->get('/dashboard', 'Dashboard::index');