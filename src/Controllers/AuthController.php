<?php

namespace App\Controllers;

class AuthController {

    // mostra a tela de login estilizada
    public function loginForm() {
        $file = __DIR__ . '/../views/login.php';

        if (file_exists($file)) {
            include $file;
            exit;
        }

        die("Erro Crítico: O arquivo views/login.php não foi encontrado.");
    }

    // processa o login.
    public function authenticate() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $pdo = getPdo();
        $stmt = $pdo->prepare("SELECT * FROM tasks.users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: /');
            exit;
        }

        $_SESSION['erro_login_direto'] = 'E-mail ou senha inválidos.';
        \App\Core\View::flash('error', 'E-mail ou senha inválidos.');

        session_write_close();

        header('Location: /login');
        exit;
    }

    public function registerForm() {
        $file = __DIR__ . '/../views/register.php';

        if (file_exists($file)) {
            include $file;
            exit;
        } else {
            die("Arquivo não encontrado em: " . $file);
        }
    }

    public function register() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($name && $email && $password) {
            $pdo = getPdo();
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO tasks.users (name, email, password) VALUES (?, ?, ?)");

            try {
                $stmt->execute([$name, $email, $hash]);
                header('Location: /login?success=1');
                exit;
            } catch (\PDOException $e) {
                header('Location: /register?error=email_exists');
                exit;
            }
        }

        header('Location: /register?error=missing_fields');
        exit;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}