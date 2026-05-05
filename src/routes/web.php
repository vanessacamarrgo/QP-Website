<?php

declare(strict_types=1);

use App\Controllers\BusCompanyController;

/** @var App\Core\Router $router */

// --- ALTERE ESTA PARTE ---
// A rota raiz agora vai para a HOME (Quero Passagem)
$router->get('/', [BusCompanyController::class, 'home']);

// A listagem/cadastro fica apenas nesta URL
$router->get('/bus-companies', [BusCompanyController::class, 'index']);
// -------------------------

// Rota de Histórico (Logs)
$router->get('/bus-companies/logs', [BusCompanyController::class, 'logs']);

// Rotas de Criação
$router->get('/bus-companies/create', [BusCompanyController::class, 'create']);
$router->post('/bus-companies', [BusCompanyController::class, 'store']);

// Rotas de Edição e Exclusão
$router->get('/bus-companies/{id}/edit', [BusCompanyController::class, 'edit']);
$router->post('/bus-companies/{id}', [BusCompanyController::class, 'update']);
$router->post('/bus-companies/{id}/delete', [BusCompanyController::class, 'destroy']);
$router->post('/bus-companies/{id}/update', [BusCompanyController::class, 'update']);