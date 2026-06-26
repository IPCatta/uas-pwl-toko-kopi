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

    public function formLogin()
    {
        $data = [
            'title' => 'Login',
            'csrf_token' => CsrfHelper::generateToken()
        ];
        $this->view('auth/login', $data);
    }

    public function login()
    {
        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Email dan password wajib diisi.');
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['auth_user_id'] = $user['id'];
            $_SESSION['auth_role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            if ($user['role'] === 'admin') {
                $this->redirect('/admin');
            } else {
                $this->redirect('/');
            }
        } else {
            $this->setFlash('error', 'Email atau password salah.');
            $this->redirect('/login');
        }
    }

    public function logout()
    {
        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Aksi tidak diizinkan.');
            $this->redirect('/');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->setFlash('success', 'Anda telah berhasil logout.');
        $this->redirect('/login');
    }

    public function profil()
    {
        $this->requireLogin();
        $user = $this->userModel->findById($_SESSION['auth_user_id']);
        
        $this->view('akun/profil', [
            'title' => 'Profil Akun',
            'user' => $user,
            'csrf_token' => CsrfHelper::generateToken()
        ]);
    }

    public function uploadFoto()
    {
        $this->requireLogin();
        
        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/akun/profil');
        }

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            require_once APP_PATH . '/helpers/UploadHelper.php';
            $uploadResult = UploadHelper::uploadProfilePhoto($_FILES['foto']);

            if ($uploadResult) {
                $this->userModel->updateFoto(
                    $_SESSION['auth_user_id'], 
                    $uploadResult['asli'], 
                    $uploadResult['thumb']
                );
                $this->setFlash('success', 'Foto profil berhasil diperbarui.');
            } else {
                $this->setFlash('error', 'Gagal mengunggah foto.');
            }
        } else {
            $this->setFlash('error', 'Silakan pilih foto terlebih dahulu.');
        }

        $this->redirect('/akun/profil');
    }
}
