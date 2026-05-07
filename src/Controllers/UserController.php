<?php

namespace App\Controllers;

use App\Core\View;
use PDOException;

class UserController {

    public function create()
    {
        return View::renderHome('users/create', []);
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            die("Todos os campos são obrigatorios");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $pdo = getPDO();
        $stmt = $pdo->prepare("INSERT INTO tasks.users (name, email, password) VALUES (?, ?, ?)");

        try {
            $stmt->execute([$name, $email, $hashedPassword]);
            header('location: /login');
            exit;
        } catch (PDOException $e) {
            die("Erro: Este e-mail já podes estar cadastrado.");

        }
    }
}