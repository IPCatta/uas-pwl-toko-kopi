<?php
require_once APP_PATH . '/models/KategoriModel.php';
require_once APP_PATH . '/helpers/CsrfHelper.php';

class KategoriController extends Controller
{
    private KategoriModel $model;

    public function __construct()
    {
        $this->requireAdmin();
        $this->model = new KategoriModel();
    }

    /** GET /admin/kategori */
    public function index(): void
    {
        $this->view('admin/kategori/index', [
            'title'      => 'Kelola Kategori',
            'kategoris'  => $this->model->getAll(),
            'csrf_token' => CsrfHelper::generateToken(),
        ]);
    }

    /** POST /admin/kategori */
    public function store(): void
    {
        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/admin/kategori');
        }

        $nama      = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        $error = $this->validateKategori($nama, $deskripsi);
        if ($error) {
            $this->setFlash('error', $error);
            $this->redirect('/admin/kategori');
        }

        if ($this->model->findByNama($nama)) {
            $this->setFlash('error', 'Nama kategori sudah digunakan.');
            $this->redirect('/admin/kategori');
        }

        if ($this->model->create(['nama' => $nama, 'deskripsi' => $deskripsi ?: null])) {
            $this->setFlash('success', 'Kategori berhasil ditambahkan.');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan sistem.');
        }

        $this->redirect('/admin/kategori');
    }

    /** POST /admin/kategori/{id}/update */
    public function update(string $id): void
    {
        $id = (int)$id;

        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/admin/kategori');
        }

        $kategori = $this->model->findById($id);
        if (!$kategori) {
            $this->setFlash('error', 'Kategori tidak ditemukan.');
            $this->redirect('/admin/kategori');
        }

        $nama      = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        $error = $this->validateKategori($nama, $deskripsi);
        if ($error) {
            $this->setFlash('error', $error);
            $this->redirect('/admin/kategori');
        }

        if ($this->model->findByNama($nama, $id)) {
            $this->setFlash('error', 'Nama kategori sudah digunakan.');
            $this->redirect('/admin/kategori');
        }

        if ($this->model->update($id, ['nama' => $nama, 'deskripsi' => $deskripsi ?: null])) {
            $this->setFlash('success', 'Kategori berhasil diperbarui.');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan sistem.');
        }

        $this->redirect('/admin/kategori');
    }

    /** POST /admin/kategori/{id}/delete */
    public function destroy(string $id): void
    {
        $id = (int)$id;

        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/admin/kategori');
        }

        $kategori = $this->model->findById($id);
        if (!$kategori) {
            $this->setFlash('error', 'Kategori tidak ditemukan.');
            $this->redirect('/admin/kategori');
        }

        if ($this->model->isUsedByProducts($id)) {
            $this->setFlash('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
            $this->redirect('/admin/kategori');
        }

        if ($this->model->delete($id)) {
            $this->setFlash('success', 'Kategori berhasil dihapus.');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan sistem.');
        }

        $this->redirect('/admin/kategori');
    }

    /* ------------------------------------------------------------------ */

    private function validateKategori(string $nama, string $deskripsi): ?string
    {
        if (strlen($nama) < 2 || strlen($nama) > 100) {
            return 'Nama kategori harus antara 2 hingga 100 karakter.';
        }
        if ($deskripsi !== '' && strlen($deskripsi) > 1000) {
            return 'Deskripsi maksimal 1000 karakter.';
        }
        return null;
    }
}
