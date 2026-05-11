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

$routes->group('admin', static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('diets', 'Admin\DietsController::index');
    $routes->get('diets/create', 'Admin\DietsController::create');
    $routes->post('diets', 'Admin\DietsController::store');
    $routes->get('diets/(:num)/edit', 'Admin\DietsController::edit/$1');
    $routes->post('diets/(:num)', 'Admin\DietsController::update/$1');
    $routes->post('diets/(:num)/delete', 'Admin\DietsController::delete/$1');
    $routes->get('activities', 'Admin\ActivitiesController::index');
    $routes->get('activities/create', 'Admin\ActivitiesController::create');
    $routes->post('activities', 'Admin\ActivitiesController::store');
    $routes->get('activities/(:num)/edit', 'Admin\ActivitiesController::edit/$1');
    $routes->post('activities/(:num)', 'Admin\ActivitiesController::update/$1');
    $routes->post('activities/(:num)/delete', 'Admin\ActivitiesController::delete/$1');
});

$routes->group('health', function($routes){
    $routes->get('metrics','Front\HealthController::getMetrics');
});

$routes->group('wallet', function($routes){
    $routes->post('credit', 'Front\WalletController::creditWallet');
    $routes->post('debit', 'Front\WalletController::debitWallet');
    $routes->get('balance', 'Front\WalletController::getBalance');
    $routes->get('transactions', 'Front\WalletController::getTransactions');
});

$routes->group('profile', function($routes){
    $routes->get('/', 'ProfileController::index');
    $routes->get('complete', 'ProfileController::complete');
    $routes->post('complete', 'ProfileController::save');
});

$routes->post('codes/redeem', 'CodesController::redeem');

$routes->group('gold', function($routes){
    $routes->get('/', 'Front\GoldController::index');
    $routes->get('status', 'Front\GoldController::status');
    $routes->post('purchase', 'Front\GoldController::purchase');
    $routes->post('activate', 'Front\GoldController::activate');
    $routes->post('deactivate', 'Front\GoldController::deactivate');
});

$routes->group('diets', function($routes){
    $routes->get('manage-test', 'Front\\DietCrudControllerTest::index');
    $routes->post('create-test', 'Front\\DietCrudControllerTest::create');
    $routes->post('delete-test/(:num)', 'Front\\DietCrudControllerTest::delete/$1');
    $routes->post('purchase', 'Front\DietPurchaseController::purchase');
});

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
    $routes->get('selected', 'Front\\RecommendationController::selected');
    $routes->post('choose/(:num)', 'Front\RecommendationController::chooseCandidate/$1');
    $routes->get('clear', 'Front\RecommendationController::clear');
});

$routes->get('/dashboard', 'Dashboard::index');
