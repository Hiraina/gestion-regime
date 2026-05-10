<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/test','TestController::index');

$routes->group('health', function($routes){
    $routes->get('metrics','Front\HealthController::getMetrics');
});

$routes->group('wallet', function($routes){
    $routes->post('credit', 'Front\WalletController::creditWallet');
    $routes->post('debit', 'Front\WalletController::debitWallet');
    $routes->get('balance', 'Front\WalletController::getBalance');
    $routes->get('transactions', 'Front\WalletController::getTransactions');
});

$routes->get('/register/step1', 'RegisterController::step1');
$routes->post('/register/step1', 'RegisterController::postStep1');

$routes->get('/register/step2', 'RegisterController::step2');
$routes->post('/register/step2', 'RegisterController::postStep2');

$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/profile/complete', 'ProfileController::complete');
$routes->post('/profile/complete', 'ProfileController::save');

$routes->get('/profile', 'ProfileController::index');




$routes->get('/goals', 'GoalController::index');
$routes->post('/goals/save', 'GoalController::save');

$routes->get('/dashboard', 'Dashboard::index');
