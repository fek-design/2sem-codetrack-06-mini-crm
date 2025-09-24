<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\MessagesController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\ProjectController;
use App\Controllers\CustomersController;
use App\Controllers\LeadsController;
use App\Router;
use App\Template;

session_start();

$template = new Template(__DIR__ . '/../views');
$router = new Router($template);

$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/projects', [ProjectController::class, 'index']);
$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact', [ContactController::class, 'post']);
$router->get('/login', [LoginController::class, 'index']);
$router->post('/login', [LoginController::class, 'login']);
$router->post('/logout', [LoginController::class, 'logout']);
$router->get('/admin/dashboard', [DashboardController::class, 'index']);
$router->get('/admin/messages', [MessagesController::class, 'index']);
$router->post('/admin/messages/{id}/toggle-read', [MessagesController::class, 'toggleRead']);
$router->post('/admin/messages/{id}/delete', [MessagesController::class, 'delete']);

// CRM Routes
$router->get('/admin/customers', [CustomersController::class, 'index']);
$router->get('/admin/customers/create', [CustomersController::class, 'create']);
$router->post('/admin/customers', [CustomersController::class, 'store']);
$router->get('/admin/customers/{id}', [CustomersController::class, 'show']);
$router->get('/admin/customers/{id}/edit', [CustomersController::class, 'edit']);
$router->post('/admin/customers/{id}', [CustomersController::class, 'update']);
$router->post('/admin/customers/{id}/interactions', [CustomersController::class, 'addInteraction']);

$router->get('/admin/leads', [LeadsController::class, 'index']);
$router->get('/admin/leads/create', [LeadsController::class, 'create']);
$router->post('/admin/leads', [LeadsController::class, 'store']);
$router->get('/admin/leads/{id}', [LeadsController::class, 'show']);
$router->get('/admin/leads/{id}/edit', [LeadsController::class, 'edit']);
$router->post('/admin/leads/{id}', [LeadsController::class, 'update']);
$router->post('/admin/leads/{id}/convert', [LeadsController::class, 'convertToCustomer']);
$router->post('/admin/leads/{id}/interactions', [LeadsController::class, 'addInteraction']);

// Dispatch current request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
