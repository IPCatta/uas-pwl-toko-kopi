<?php
/**
 * CsrfHelper — kedai-kopi
 *
 * Menangani pembuatan dan verifikasi token CSRF.
 */
class CsrfHelper
{
    /**
     * Menghasilkan token CSRF dan menyimpannya di session.
     *
     * @return string
     */
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Memverifikasi token CSRF dari input dengan yang ada di session.
     *
     * @param string $token
     * @return bool
     */
    public static function verifyToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sessionToken = $_SESSION['csrf_token'] ?? '';
        return hash_equals($sessionToken, $token);
    }
}
