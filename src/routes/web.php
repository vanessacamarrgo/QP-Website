<?php

declare(strict_types=1);

use App\Controllers\BusCompanyController;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;

/** @var App\Core\Router $router */

// Home pública
$router->get('/', [BusCompanyController::class, 'home']);

//Bus Companies
$router->get('/bus-companies',                        [BusCompanyController::class, 'index']);
$router->get('/bus-companies/logs',                   [BusCompanyController::class, 'logs']);
$router->get('/bus-companies/create',                 [BusCompanyController::class, 'create']);
$router->post('/bus-companies',                       [BusCompanyController::class, 'store']);
$router->get('/bus-companies/{id}',                   [BusCompanyController::class, 'show']);
$router->get('/bus-companies/{id}/edit',              [BusCompanyController::class, 'edit']);
$router->post('/bus-companies/{id}/update',           [BusCompanyController::class, 'update']);
$router->post('/bus-companies/{id}',                  [BusCompanyController::class, 'update']);
$router->post('/bus-companies/{id}/delete',           [BusCompanyController::class, 'destroy']);
$router->post('/bus-companies/{id}/restore',          [BusCompanyController::class, 'restore']);

// Users
$router->get('/users',                                [UserController::class, 'index']);
$router->get('/users/create',                         [UserController::class, 'create']);
$router->post('/users',                               [UserController::class, 'store']);
$router->get('/users/{id}',                           [UserController::class, 'show']);
$router->get('/users/{id}/edit',                      [UserController::class, 'edit']);
$router->post('/users/{id}/update',                   [UserController::class, 'update']);
$router->post('/users/{id}/delete',                   [UserController::class, 'destroy']);
$router->post('/users/{id}/restore',                  [UserController::class, 'restore']);

//Auth

$router->get('/login',    [AuthController::class, 'loginForm']);
$router->post('/login',   [AuthController::class, 'authenticate']);
$router->get('/logout',   [AuthController::class, 'logout']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register',[AuthController::class, 'register']);