<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// RH routes
$routes->get('rh', 'RhController::index');
$routes->post('rh/approuver/(:num)', 'RhController::approve/$1');
$routes->post('rh/refuser/(:num)', 'RhController::refuse/$1');
// Auth routes
$routes->get('auth/login', 'AuthController::login');
$routes->post('auth/attempt', 'AuthController::attempt');
$routes->get('auth/logout', 'AuthController::logout');
