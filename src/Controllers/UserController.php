<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\UserService;

final class UserController
{
    private UserService $service;

    public function __construct(?UserService $service = null)
    {
        $this->service = $service ?? new UserService();
    }

    private function checkAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $emailLogado = strtolower($_SESSION['user_email'] ?? '');

        if (
            $emailLogado !== 'adm@gmail.com' &&
            $emailLogado !== 'adm2@gmail.com' &&
            $emailLogado !== 'adm3@gmail.com'
        ) {
            View::flash('error', 'Acesso negado.');
            header('Location: /');
            exit;
        }
    }

    public function index(): void
    {
        $this->checkAdmin();

        $name   = (string) ($_GET['name'] ?? '');
        $status = (string) ($_GET['status'] ?? '');
        $page   = max(1, (int) ($_GET['page'] ?? 1));

        $result = $this->service->all($name, $status, $page);

        View::render('users/index', [
            'title'        => 'Usuários',
            'users'        => $result['items'],
            'pagination'   => $result,
            'filterName'   => $name,
            'filterStatus' => $status,
        ]);
    }

    /** Visualização de um único usuário com histórico */
    public function show(int $id): void
    {
        $this->checkAdmin();

        $user = $this->service->find($id);
        if (!$user) {
            View::redirect('/users');
            return;
        }

        $logs = $this->service->getLogsForEntity($id);

        View::render('users/show', [
            'title' => 'Usuário: ' . $user->name,
            'user'  => $user,
            'logs'  => $logs,
        ]);
    }

    public function create(): void
    {
        $this->checkAdmin();

        View::render('users/create', [
            'title'  => 'Novo Usuário',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function store(): void
    {
        $this->checkAdmin();

        $data = [
            'name'     => trim((string) ($_POST['name'] ?? '')),
            'email'    => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'status'   => (string) ($_POST['status'] ?? 'active'),
        ];

        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('users/create', ['title' => 'Novo Usuário', 'errors' => $errors, 'old' => $data]);
            return;
        }

        try {
            $this->service->create($data);
            View::flash('success', 'Usuário criado com sucesso.');
            View::redirect('/users');
        } catch (\PDOException $e) {
            $msg = str_contains($e->getMessage(), 'Duplicate') ? 'Este e-mail já está cadastrado.' : 'Erro ao criar usuário.';
            View::render('users/create', ['title' => 'Novo Usuário', 'errors' => [$msg], 'old' => $data]);
        }
    }

    public function edit(int $id): void
    {
        $this->checkAdmin();

        $user = $this->service->find($id);
        if (!$user) {
            View::redirect('/users');
            return;
        }

        View::render('users/edit', [
            'title'  => 'Editar Usuário',
            'user'   => $user,
            'errors' => [],
            'old'    => (array) $user,
        ]);
    }

    public function update(int $id): void
    {
        $this->checkAdmin();

        $user = $this->service->find($id);
        if (!$user) {
            View::redirect('/users');
            return;
        }

        $data = [
            'name'     => trim((string) ($_POST['name'] ?? '')),
            'email'    => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'status'   => (string) ($_POST['status'] ?? 'active'),
        ];

        $errors = $this->validate($data, forUpdate: true);

        if ($errors !== []) {
            View::render('users/edit', ['title' => 'Editar Usuário', 'errors' => $errors, 'user' => $user, 'old' => $data]);
            return;
        }

        try {
            $this->service->update($id, $data);
            View::flash('success', 'Usuário atualizado.');
            View::redirect('/users');
        } catch (\PDOException $e) {
            $msg = str_contains($e->getMessage(), 'Duplicate') ? 'Este e-mail já está em uso.' : 'Erro ao atualizar.';
            View::render('users/edit', ['title' => 'Editar Usuário', 'errors' => [$msg], 'user' => $user, 'old' => $data]);
        }
    }

    public function destroy(int $id): void
    {
        $this->checkAdmin();

        $this->service->delete($id);
        View::flash('success', 'Usuário removido.');
        View::redirect('/users');
    }

    public function restore(int $id): void
    {
        $this->checkAdmin();

        $this->service->restore($id);
        View::flash('success', 'Usuário restaurado com sucesso.');
        View::redirect('/users?status=deleted');
    }

    private function validate(array $data, bool $forUpdate = false): array
    {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'O nome é obrigatório.';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail inválido.';
        }
        if (!$forUpdate && empty($data['password'])) {
            $errors[] = 'A senha é obrigatória.';
        }
        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'A senha deve ter ao menos 6 caracteres.';
        }
        return $errors;
    }
}