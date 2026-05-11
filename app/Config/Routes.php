<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth\LoginController::index');
$routes->get('auth/login', 'Auth\LoginController::index');
$routes->get('register', 'Auth\RegisterController::step1');
$routes->get('register/step1', 'Auth\RegisterController::step1');
$routes->post('register/step1', 'Auth\RegisterController::saveStep1');
$routes->get('register/health', 'Auth\RegisterController::health');
$routes->post('register/health', 'Auth\RegisterController::saveHealth');
$routes->get('profile/complete', 'Front\ProfileController::complete');
$routes->post('profile/complete', 'Front\ProfileController::saveCompletion');
$routes->get('dashboard', static function () {
    return 'Dashboard utilisateur a creer plus tard.';
});

$routes->group('admin', static function ($routes) {
    $routes->get('diets', 'Admin\DietsController::index');
    $routes->get('diets/create', 'Admin\DietsController::create');
    $routes->post('diets', 'Admin\DietsController::store');
    $routes->get('diets/(:num)/edit', 'Admin\DietsController::edit/$1');
    $routes->post('diets/(:num)', 'Admin\DietsController::update/$1');
    $routes->post('diets/(:num)/delete', 'Admin\DietsController::delete/$1');
});
