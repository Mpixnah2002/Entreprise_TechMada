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
