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


    private function checkAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $emailLogado = strtolower($_SESSION['user_email'] ?? '');

        if (
            $emailLogado !== 'adm@gmail.com' &&
            $emailLogado !== 'adm2@gmail.com' &&
            $emailLogado !== 'adm3@gmail.com'
        ) {
            View::flash('error', 'Acesso negado. Você não é o administrador.');
            header('Location: /');
            exit;
        }
    }


    public function home(): void
    {
        $companies = $this->service->all('', 'active');

        View::renderHome('home', [
            'companies' => $companies
        ]);
    }


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

    public function destroy(int $id): void
    {
        $this->checkAdmin(); // Protegido

        $this->service->delete($id);
        View::flash('success', 'Viação removida.');
        View::redirect('/bus-companies');
    }

    public function logs()
    {
        $this->checkAdmin();

        // Captura todos os filtros da URL
        $filters = [
            'id'        => $_GET['id'] ?? null,
            'user_id'   => $_GET['user_id'] ?? null,
            'bus_id'    => $_GET['bus_id'] ?? null,
            'bus_name'  => $_GET['bus_name'] ?? null,
            'action'    => $_GET['action'] ?? null,
            'date'      => $_GET['date'] ?? null,
        ];

        $logs = $this->service->getLogs($filters);

        View::render('logs', ['logs' => $logs, 'filters' => $filters]);
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

    public function store(): void
    {
        $this->checkAdmin();

        $data = $this->capturePostData();
        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('create', ['title' => 'Nova Viação', 'errors' => $errors, 'old' => $data]);
            return;
        }

        try {
            $data['logo'] = $this->handleUpload(); // Faz o upload e valida MIME
            $this->service->create($data);

            View::flash('success', 'Viação criada com sucesso.');
            View::redirect('/bus-companies');
        } catch (\Exception $e) {
            View::render('create', ['title' => 'Nova Viação', 'errors' => [$e->getMessage()], 'old' => $data]);
        }
    }

    public function update(int $id): void
    {
        $this->checkAdmin();

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

        try {
            $newLogo = $this->handleUpload();
            // Se $newLogo for null, significa que o usuário não subiu nada novo.
            // Então usamos a logo que já estava no banco ($company->logo).
            $data['logo'] = $newLogo ?? $company->logo;

            $this->service->update($id, $data);

            View::flash('success', 'Viação atualizada.');
            View::redirect('/bus-companies');
        } catch (\Exception $e) {
            View::render('edit', ['title' => 'Editar', 'errors' => [$e->getMessage()], 'company' => $company, 'old' => $data]);
        }
    }

    /**
     * Lógica de Upload Segura com validação de MIME Type real
     */
    private function handleUpload(): ?string
    {
        // 1. Se não houver arquivo novo, mantém o antigo
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            return $_POST['old_logo'] ?? null;
        }

        $file = $_FILES['logo'];

        // 2. Validação de segurança (MIME TYPE)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedMimes)) {
            throw new \Exception("Apenas imagens JPG ou PNG são permitidas.");
        }

        // 3. CAMINHO FÍSICO (Para o PHP salvar o arquivo)
        // DOCUMENT_ROOT aponta para a sua pasta 'public' que o servidor web usa
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // 4. Nome aleatório para evitar conflitos
        $extension = $allowedMimes[$mimeType];
        $fileName = bin2hex(random_bytes(10)) . '.' . $extension;
        $targetFile = $uploadDir . $fileName;

        // 5. Move o arquivo
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return 'uploads/' . $fileName;
        }

        return $_POST['old_logo'] ?? null;
    }

}