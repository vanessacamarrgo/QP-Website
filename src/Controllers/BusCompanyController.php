<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\BusCompanyService;

final class BusCompanyController
{
    private BusCompanyService $service;

    public function __construct(?BusCompanyService $service = null)
    {
        $this->service = $service ?? new BusCompanyService();
    }

    public function index(): void
    {
        $name = (string) ($_GET['name'] ?? '');
        $status = (string) ($_GET['status'] ?? '');

        View::render('index', [
            'title' => 'Viações',
            'companies' => $this->service->all($name, $status),
            'filterName' => $name,
            'filterStatus' => $status,
            'busCompanyNamesJson' => json_encode($this->service->allNames()) // Adicionado para o seu autocomplete
        ]);
    }

    /** Abre o formulário de criação */
    public function create(): void
    {
        View::render('create', [
            'title' => 'Nova Viação',
            'errors' => [],
            'old' => []
        ]);
    }

    public function store(): void
    {
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

    /** Abre o formulário de edição */
    public function edit(int $id): void
    {
        $company = $this->service->find($id);
        if (!$company) {
            View::redirect('/bus-companies');
            return;
        }

        View::render('edit', [
            'title' => 'Editar Viação',
            'company' => $company,
            'errors' => [],
            'old' => [
                'name' => $company->name,
                'url' => $company->url,
                'city' => $company->city,
                'status' => $company->status
            ]
        ]);
    }

    public function update(int $id): void
    {
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

        $data['logo'] = $this->handleUpload() ?? $company->logo;
        $this->service->update($id, $data);

        View::flash('success', 'Viação atualizada.');
        View::redirect('/bus-companies');
    }

    /** Exclui uma viação */
    public function destroy(int $id): void
    {
        $this->service->delete($id);
        View::flash('success', 'Viação removida.');
        View::redirect('/bus-companies');
    }

    /** Abre a página de histórico */
    public function logs(): void
    {
        $logs = $this->service->getLogs();

        View::render('logs', [
            'title' => 'Histórico de Alterações',
            'logs' => $logs
        ]);
    }
    private function capturePostData(): array
    {
        return [
            'name'   => trim((string) ($_POST['name'] ?? '')),
            'url'    => trim((string) ($_POST['url'] ?? '')),
            'city'   => trim((string) ($_POST['city'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'active'),
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

    private function handleUpload(): ?string
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== 0) return null;
        $name = uniqid() . '_' . basename($_FILES['logo']['name']);
        $path = 'uploads/' . $name;
        move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/../../public/' . $path);
        return $path;
    }
} // <--- ESTA CHAVE ESTAVA FALTANDO!