<?php

declare(strict_types=1);

/** Arquivo de registro de rotas web adaptado do padrão Raissa Kuzer. */
use App\Controllers\BusCompanyController;

/** @var App\Core\Router $router */

// Rota inicial e listagem
$router->get('/', [BusCompanyController::class, 'index']);
$router->get('/bus-companies', [BusCompanyController::class, 'index']);

// Rota de Histórico (Logs) - Para o botão "Histórico de Alterações"
$router->get('/bus-companies/logs', [BusCompanyController::class, 'logs']);

// Rotas de Criação - Para o botão "+ Nova Viação"
$router->get('/bus-companies/create', [BusCompanyController::class, 'create']);
$router->post('/bus-companies', [BusCompanyController::class, 'store']);

// Rotas de Edição e Exclusão
$router->get('/bus-companies/{id}/edit', [BusCompanyController::class, 'edit']);
$router->post('/bus-companies/{id}', [BusCompanyController::class, 'update']);
$router->post('/bus-companies/{id}/delete', [BusCompanyController::class, 'destroy']);
$router->post('/bus-companies/{id}/update', [BusCompanyController::class, 'update']);