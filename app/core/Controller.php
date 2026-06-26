<?php
class Controller
{
    protected function view(string $viewName, array $data = []): void {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $viewName . '.php';
        if (file_exists($viewFile)) require $viewFile;
        else { http_response_code(500); echo "View not found"; }
    }

    protected function redirect(string $path): void {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['auth_user_id'])) $this->redirect('/login');
    }

    protected function requireAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['auth_user_id']) || ($_SESSION['auth_role'] ?? '') !== 'admin') {
            $this->redirect('/login');
        }
    }

    protected function setFlash(string $type, string $message): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function getFlash(): ?array {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
