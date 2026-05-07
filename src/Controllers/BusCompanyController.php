<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\BusCompanyService;

final class BusCompanyController
{
    private BusCompanyService $service;

    /**
     * O Construtor agora é público. Ele não barra ninguém aqui
     * para permitir que a Home Page funcione para todos.
     */
    public function __construct(?BusCompanyService $service = null)
    {
        $this->service = $service ?? new BusCompanyService();
    }

    /**
     * MÉTODO PORTEIRO: Verifica se é Admin.
     * Chamamos isso dentro das funções que queremos proteger.
     */
    private function checkAdmin(): void
    {
        // Garante que a sessão está aberta
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // DEBUG TEMPORÁRIO: Se o botão não funcionar, descomente a linha abaixo para ver o erro
        // var_dump($_SESSION); die();

        // Verifica se o e-mail existe na sessão.
        // DICA: mude 'adm@email.com' para o e-mail que você REALMENTE usa no banco
        $emailLogado = $_SESSION['user_email'] ?? '';

        if (strtolower($emailLogado) !== 'adm@gmail.com') {
            View::flash('error', 'Acesso negado. Você não é o administrador.');
            header('Location: /');
            exit;
        }
    }

    /**
     * HOME PAGE: Aberta para o público e filtra apenas Ativas.
     */
    public function home(): void
    {
        // Filtramos por 'active' para não mostrar viações inativas na vitrine
        $companies = $this->service->all('', 'active');

        View::renderHome('home', [
            'companies' => $companies
        ]);
    }

    /**
     * LISTA DO PAINEL ADM
     */
    public function index(): void
    {
        $this->checkAdmin(); // Protegido

        $name = (string)($_GET['name'] ?? '');
        $status = (string)($_GET['status'] ?? '');

        View::render('index', [
            'title' => 'Viações',
            'companies' => $this->service->all($name, $status),
            'filterName' => $name,
            'filterStatus' => $status,
            'busCompanyNamesJson' => json_encode($this->service->allNames())
        ]);
    }

    public function create(): void
    {
        $this->checkAdmin(); // Protegido

        View::render('create', [
            'title' => 'Nova Viação',
            'errors' => [],
            'old' => []
        ]);
    }

    public function store(): void
    {
        $this->checkAdmin(); // Protegido

        $data = $this->capturePostData();
        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('create', ['title' => 'Nova Viação', 'errors' => $errors, 'old' => $data]);
            return;
        }

        $data['logo'] = $this->handleUpload();
        $this->service->create($data);

        View::flash('success', 'Viação criada com sucesso.');
        View::redirect('/bus-companies');
    }

    public function edit(int $id): void
    {
        $this->checkAdmin(); // Protegido

        $company = $this->service->find($id);
        if (!$company) {
            View::redirect('/bus-companies');
            return;
        }

        View::render('edit', [
            'title' => 'Editar Viação',
            'company' => $company,
            'errors' => [],
            'old' => (array)$company
        ]);
    }

    public function update(int $id): void
    {
        $this->checkAdmin(); // Protegido

        $company = $this->service->find($id);
        if (!$company) {
            View::redirect('/bus-companies');
            return;
        }

        $data = $this->capturePostData();
        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('edit', ['title' => 'Editar', 'errors' => $errors, 'company' => $company, 'old' => $data]);
            return;
        }

        $newLogo = $this->handleUpload();
        $data['logo'] = $newLogo ?? $company->logo;

        $this->service->update($id, $data);

        View::flash('success', 'Viação atualizada.');
        View::redirect('/bus-companies');
    }

    public function destroy(int $id): void
    {
        $this->checkAdmin(); // Protegido

        $this->service->delete($id);
        View::flash('success', 'Viação removida.');
        View::redirect('/bus-companies');
    }

    public function logs(): void
    {
        $this->checkAdmin(); // Protegido

        $logs = $this->service->getLogs();
        View::render('logs', [
            'title' => 'Histórico de Alterações',
            'logs' => $logs
        ]);
    }

    private function capturePostData(): array
    {
        return [
            'name' => trim((string)($_POST['name'] ?? '')),
            'url' => trim((string)($_POST['url'] ?? '')),
            'city' => trim((string)($_POST['city'] ?? '')),
            'status' => (string)($_POST['status'] ?? 'active'),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') $errors[] = 'O nome é obrigatório.';
        if ($data['url'] !== '' && !filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'URL inválida.';
        }
        return $errors;
    }

    /**
     * Função de Upload Ajustada para Docker Desktop
     */
    private function handleUpload(): ?string
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['logo']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
            return $fileName;
        }

        return null;
    }
}