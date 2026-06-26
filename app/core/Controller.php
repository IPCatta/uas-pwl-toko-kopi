<?php
/**
 * Base Controller — kedai-kopi
 *
 * Menyediakan helper umum untuk semua controller:
 *   - view()          : render file view dengan data
 *   - redirect()      : redirect ke path tertentu
 *   - json()          : kirim response JSON (untuk API)
 *   - requireLogin()  : guard — wajib login
 *   - requireAdmin()  : guard — wajib role admin
 */
class Controller
{
    /**
     * Render file view dan kirimkan data ke view.
     *
     * @param string $viewName  Nama view relatif terhadap app/views/ (tanpa .php)
     *                          Contoh: 'katalog/index', 'errors/404'
     * @param array  $data      Data yang dikirim ke view (di-extract jadi variabel)
     * @return void
     */
    protected function view(string $viewName, array $data = []): void
    {
        // Extract data agar bisa dipakai sebagai variabel di view
        // Misal: $data['title'] → $title
        extract($data);

        $viewFile = APP_PATH . '/views/' . $viewName . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            http_response_code(500);
            echo "View '{$viewName}' tidak ditemukan.";
        }
    }

    /**
     * Redirect ke path tertentu.
     *
     * @param string $path  Path relatif dari BASE_URL (misal '/login', '/admin')
     * @return void
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    /**
     * Kirim response JSON.
     * Otomatis set Content-Type header.
     *
     * @param array $data       Data yang akan di-encode ke JSON
     * @param int   $statusCode HTTP status code (default 200)
     * @return void
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Guard: wajib login.
     * Jika user belum login (tidak ada sesi), redirect ke halaman login.
     *
     * @return void
     */
    protected function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['auth_user_id'])) {
            $this->redirect('/login');
        }
    }

    /**
     * Guard: wajib role admin.
     * Jika user bukan admin, redirect ke halaman login.
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['auth_user_id']) || ($_SESSION['auth_role'] ?? '') !== 'admin') {
            $this->redirect('/login');
        }
    }

    /**
     * Set flash message ke session.
     * Pesan ditampilkan sekali lalu otomatis hilang.
     *
     * @param string $type    Tipe pesan (success, error, warning, info)
     * @param string $message Isi pesan
     * @return void
     */
    protected function setFlash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /**
     * Ambil dan hapus flash message dari session.
     *
     * @return array|null  ['type' => '...', 'message' => '...'] atau null
     */
    protected function getFlash(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
