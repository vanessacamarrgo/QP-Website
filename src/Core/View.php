<?php

declare(strict_types=1);

namespace App\Core;

/** Renderiza views com layout e suporte a flash message. */
final class View
{
    /** @param array<string, mixed> $data Dados passados para a view. */
    public static function render(string $view, array $data = []): void
    {
        $basePath = dirname(__DIR__);
        $viewFile = $basePath . '/views/' . $view . '.php';
        $layoutFile = $basePath . '/views/_layout.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View não encontrada: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        if (!is_file($layoutFile)) {
            http_response_code(500);
            echo 'Layout não encontrado.';
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        require $layoutFile;
    }

    /** * Renderiza a Home sem o layout padrão para evitar conflitos de CSS.
     * @param array<string, mixed> $data
     */
    public static function renderHome(string $view, array $data = []): void
    {
        $basePath = dirname(__DIR__);
        $viewFile = $basePath . '/views/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View da Home não encontrada.';
            return;
        }

        // Extrai os dados ($companies, etc) para ficarem disponíveis no HTML
        extract($data, EXTR_SKIP);

        // Carrega apenas o arquivo da home diretamente
        require $viewFile;
    }

    /** Redireciona para um path e encerra a requisicao. */
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    /** Salva uma flash message na sessao para o proximo request. */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /** @return array{type: string, message: string}|null Le e remove a flash message. */
    public static function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        if (!is_array($flash)) {
            return null;
        }

        if (!isset($flash['type'], $flash['message'])) {
            return null;
        }

        return [
            'type' => (string) $flash['type'],
            'message' => (string) $flash['message'],
        ];
    }

    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Se chegou aqui, está logado. Segue o baile...
        return View::renderHome('create', []);
    }
}