<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->post('home/edit/(:num)', 'Home::edit/$1');
$routes->post('home/tambah', 'Home::tambah');
$routes->get('home/hapus/(:num)', 'Home::hapus/$1');