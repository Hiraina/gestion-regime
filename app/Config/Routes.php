<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('health', function($routes){
    $routes->get('metrics','Front\HealthController::getMetrics');
});

$routes->group('wallet', function($routes){
    $routes->post('credit', 'Front\WalletController::creditWallet');
    $routes->post('debit', 'Front\WalletController::debitWallet');
    $routes->get('balance', 'Front\WalletController::getBalance');
    $routes->get('transactions', 'Front\WalletController::getTransactions');
});

$routes->group('register', function($routes){
    $routes->get('step1', 'RegisterController::step1');
    $routes->post('step1', 'RegisterController::postStep1');

    $routes->get('step2', 'RegisterController::step2');
    $routes->post('step2', 'RegisterController::postStep2');
});


$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');

$routes->group('profile', function($routes){
    $routes->get('/', 'ProfileController::index');
    $routes->get('complete', 'ProfileController::complete');
    $routes->post('complete', 'ProfileController::save');
});

$routes->post('codes/redeem', 'CodesController::redeem');

$routes->group('goals', function($routes){
    $routes->get('/', 'GoalController::index');
    $routes->post('save', 'GoalController::save');
});

$routes->group('recommendations', function($routes){
    $routes->get('/', 'Front\RecommendationController::index');
    $routes->get('step1', 'Front\RecommendationController::step1');
    $routes->post('step1', 'Front\RecommendationController::saveStep1');
    $routes->get('step2', 'Front\RecommendationController::step2');
    $routes->post('step2', 'Front\RecommendationController::saveStep2');
    $routes->get('step3', 'Front\RecommendationController::step3');
    $routes->post('step3', 'Front\RecommendationController::saveStep3');
    $routes->get('step4', 'Front\RecommendationController::step4');
    $routes->post('submit', 'Front\RecommendationController::submit');
    $routes->get('candidates', 'Front\RecommendationController::candidates');
    $routes->post('choose/(:num)', 'Front\RecommendationController::chooseCandidate/$1');
    $routes->get('clear', 'Front\RecommendationController::clear');
});

$routes->get('/dashboard', 'Dashboard::index');
