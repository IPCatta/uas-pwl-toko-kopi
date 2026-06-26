<?php
require_once APP_PATH . '/models/UserModel.php';
require_once APP_PATH . '/helpers/CsrfHelper.php';

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function formRegister()
    {
        $data = [
            'title' => 'Register',
            'csrf_token' => CsrfHelper::generateToken()
        ];
        $this->view('auth/register', $data);
    }

    public function register()
    {
        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/register');
        }

        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validasi
        if (strlen($nama) < 2 || strlen($nama) > 100) {
            $this->setFlash('error', 'Nama harus antara 2 hingga 100 karakter.');
            $this->redirect('/register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Format email tidak valid.');
            $this->redirect('/register');
        }
        if (strlen($password) < 8) {
            $this->setFlash('error', 'Password minimal 8 karakter.');
            $this->redirect('/register');
        }
        if ($password !== $password_confirm) {
            $this->setFlash('error', 'Konfirmasi password tidak cocok.');
            $this->redirect('/register');
        }

        // Cek unik
        if ($this->userModel->findByEmail($email)) {
            $this->setFlash('error', 'Email sudah terdaftar.');
            $this->redirect('/register');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $success = $this->userModel->create([
            'nama' => $nama,
            'email' => $email,
            'password' => $hash,
            'role' => 'pelanggan'
        ]);

        if ($success) {
            $this->setFlash('success', 'Registrasi berhasil. Silakan login.');
            $this->redirect('/login');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan sistem.');
            $this->redirect('/register');
        }
    }
}
