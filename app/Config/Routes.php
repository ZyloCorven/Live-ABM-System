<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');

// ---- Auth ----
$routes->get('/login', 'Auth::showLogin');
$routes->post('/login', 'Auth::login');
$routes->get('/register', 'Auth::showRegister');
$routes->post('/register', 'Auth::register');
$routes->get('/logout', 'Auth::logout');

// ---- Public auction browsing ----
$routes->get('/auctions', 'Auctions::index');
$routes->get('/auctions/(:num)', 'Auctions::show/$1');

// ---- Authenticated area ----
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'Dashboard::index');

    // Bidding (AJAX)
    $routes->post('/auctions/(:num)/bid', 'Bids::place/$1');
    $routes->get('/auctions/(:num)/state', 'Bids::state/$1');

    // Invoice viewing — open to buyer, seller, or admin (checked in-controller)
    $routes->get('/pos/orders/(:num)/invoice', 'Pos::invoice/$1');
});

// ---- Seller + Admin: manage auctions ----
$routes->group('', ['filter' => 'role:seller,admin'], static function ($routes) {
    $routes->get('/auctions/create', 'Auctions::create');
    $routes->post('/auctions', 'Auctions::store');
    $routes->get('/auctions/(:num)/edit', 'Auctions::edit/$1');
    $routes->post('/auctions/(:num)/edit', 'Auctions::update/$1');
    $routes->get('/my-auctions', 'Auctions::mine');
});

// ---- POS (seller + admin) ----
$routes->group('pos', ['filter' => 'role:seller,admin'], static function ($routes) {
    $routes->get('/', 'Pos::index');
    $routes->get('orders/(:num)', 'Pos::show/$1');
    $routes->post('orders/(:num)/payment', 'Pos::recordPayment/$1');
    $routes->post('orders/(:num)/ship', 'Pos::markShipped/$1');
    $routes->post('orders/(:num)/complete', 'Pos::markCompleted/$1');
    $routes->match(['get', 'post'], 'manual-sale', 'Pos::createManual');
});

// ---- Admin only ----
$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('users', 'Admin\Users::index');
    $routes->post('users/(:num)/toggle', 'Admin\Users::toggleActive/$1');
    $routes->post('users/(:num)/role', 'Admin\Users::updateRole/$1');

    $routes->get('auctions', 'Admin\Auctions::index');
    $routes->post('auctions/(:num)/force-end', 'Admin\Auctions::forceEnd/$1');
    $routes->post('auctions/(:num)/cancel', 'Admin\Auctions::cancel/$1');

    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings', 'Admin\Settings::update');
});
