<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\BusCompanyService;

final class HomeController
{
    private BusCompanyService $service;

    public function __construct()
    {
        // Aqui NÃO tem trava de segurança, então qualquer um acessa
        $this->service = new BusCompanyService();
    }

    public function home(): void
    {
        // 1. TESTE DE DEBUG (Pode apagar essa linha depois que confirmar que funcionou)
        echo "<h1 style='color:red; background:yellow; position:fixed; top:0; z-index:9999;'>DEBUG: ESTOU NO HOMECONTROLLER!</h1>";

        // 2. O filtro 'active' que vai para o Service
        $companies = $this->service->all('', 'active');

        View::renderHome('home', [
            'companies' => $companies
        ]);
    }
}