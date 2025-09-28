<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\DashboardController;
use App\Controllers\LoginController;
use App\Controllers\CustomersController;
use App\Controllers\LeadsController;
use App\Controllers\RedirectController;
use App\Router;
use App\Template;

session_start();

$template = new Template(__DIR__ . '/../views');
$router = new Router($template);

// Root redirect based on authentication status
$router->get('/', [RedirectController::class, 'index']);

// Authentication routes
$router->get('/login', [LoginController::class, 'index']);
$router->post('/login', [LoginController::class, 'login']);
$router->post('/logout', [LoginController::class, 'logout']);

// CRM Routes
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/customers', [CustomersController::class, 'index']);
$router->get('/customers/create', [CustomersController::class, 'create']);
$router->post('/customers', [CustomersController::class, 'store']);
$router->get('/customers/{id}', [CustomersController::class, 'show']);
$router->get('/customers/{id}/edit', [CustomersController::class, 'edit']);
$router->post('/customers/{id}', [CustomersController::class, 'update']);
$router->delete('/customers/{id}', [CustomersController::class, 'delete']);
$router->post('/customers/{id}/interactions', [CustomersController::class, 'addInteraction']);

$router->get('/leads', [LeadsController::class, 'index']);
$router->get('/leads/create', [LeadsController::class, 'create']);
$router->post('/leads', [LeadsController::class, 'store']);
$router->get('/leads/{id}', [LeadsController::class, 'show']);
$router->get('/leads/{id}/edit', [LeadsController::class, 'edit']);
$router->post('/leads/{id}', [LeadsController::class, 'update']);
$router->delete('/leads/{id}', [LeadsController::class, 'delete']);
$router->post('/leads/{id}/convert', [LeadsController::class, 'convertToCustomer']);
$router->post('/leads/{id}/interactions', [LeadsController::class, 'addInteraction']);

// Dispatch current request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
