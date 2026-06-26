<?php
require_once APP_PATH . '/models/ProdukModel.php';
require_once APP_PATH . '/models/KategoriModel.php';
require_once APP_PATH . '/helpers/CsrfHelper.php';
require_once APP_PATH . '/helpers/UploadHelper.php';

class ProdukController extends Controller
{
    private ProdukModel   $produkModel;
    private KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->produkModel   = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
    }

    /** GET /admin/produk */
    public function index(): void
    {
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $totalItems = $this->produkModel->countAll();
        $totalPages = (int)ceil($totalItems / 10) ?: 1;
        $page       = min($page, $totalPages);

        $this->view('admin/produk/index', [
            'title'      => 'Kelola Produk',
            'produks'    => $this->produkModel->getAll($page),
            'page'       => $page,
            'totalPages' => $totalPages,
            'csrf_token' => CsrfHelper::generateToken(),
        ]);
    }

    /** GET /admin/produk/tambah */
    public function formTambah(): void
    {
        $this->view('admin/produk/tambah', [
            'title'      => 'Tambah Produk',
            'kategoris'  => $this->kategoriModel->getAll(),
            'csrf_token' => CsrfHelper::generateToken(),
        ]);
    }

    /** GET /admin/produk/{id}/edit */
    public function formEdit(string $id): void
    {
        $produk = $this->produkModel->findById((int)$id);
        if (!$produk) {
            $this->setFlash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/produk');
        }

        $this->view('admin/produk/edit', [
            'title'      => 'Edit Produk',
            'produk'     => $produk,
            'kategoris'  => $this->kategoriModel->getAll(),
            'csrf_token' => CsrfHelper::generateToken(),
        ]);
    }

    /** POST /admin/produk */
    public function store(): void
    {
        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/admin/produk/tambah');
        }

        $data  = $this->extractPostData();
        $error = $this->validateProduk($data);
        if ($error) {
            $this->setFlash('error', $error);
            $this->redirect('/admin/produk/tambah');
        }

        /* Foto — opsional, tapi disarankan */
        $uploadResult = null;
        if (!empty($_FILES['foto']['name'])) {
            $uploadResult = UploadHelper::upload($_FILES['foto'], 'produk');
            if (!$uploadResult) {
                $this->setFlash('error', UploadHelper::getLastError() ?: 'Gagal mengunggah foto.');
                $this->redirect('/admin/produk/tambah');
            }
        }

        $row = [
            'kategori_id' => $data['kategori_id'],
            'nama'        => $data['nama'],
            'deskripsi'   => $data['deskripsi'] ?: null,
            'harga'       => $data['harga'],
            'berat'       => $data['berat'],
            'stok'        => $data['stok'],
            'foto'        => $uploadResult['asli']    ?? null,
            'foto_resized'=> $uploadResult['resized'] ?? null,
            'foto_thumb'  => $uploadResult['thumb']   ?? null,
            'foto_width'  => $uploadResult['width']   ?? null,
            'foto_height' => $uploadResult['height']  ?? null,
            'aktif'       => $data['aktif'],
        ];

        if ($this->produkModel->create($row)) {
            $this->setFlash('success', 'Produk berhasil ditambahkan.');
            $this->redirect('/admin/produk');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan sistem.');
            $this->redirect('/admin/produk/tambah');
        }
    }

    /** POST /admin/produk/{id}/update */
    public function update(string $id): void
    {
        $id = (int)$id;

        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/admin/produk/' . $id . '/edit');
        }

        $produk = $this->produkModel->findById($id);
        if (!$produk) {
            $this->setFlash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/produk');
        }

        $data  = $this->extractPostData();
        $error = $this->validateProduk($data);
        if ($error) {
            $this->setFlash('error', $error);
            $this->redirect('/admin/produk/' . $id . '/edit');
        }

        /* Foto baru (opsional saat update) */
        $uploadResult = null;
        if (!empty($_FILES['foto']['name'])) {
            $uploadResult = UploadHelper::upload($_FILES['foto'], 'produk');
            if (!$uploadResult) {
                $this->setFlash('error', UploadHelper::getLastError() ?: 'Gagal mengunggah foto.');
                $this->redirect('/admin/produk/' . $id . '/edit');
            }
        }

        $row = [
            'kategori_id' => $data['kategori_id'],
            'nama'        => $data['nama'],
            'deskripsi'   => $data['deskripsi'] ?: null,
            'harga'       => $data['harga'],
            'berat'       => $data['berat'],
            'stok'        => $data['stok'],
            'aktif'       => $data['aktif'],
        ];

        if ($uploadResult) {
            $row['foto']        = $uploadResult['asli'];
            $row['foto_resized']= $uploadResult['resized'];
            $row['foto_thumb']  = $uploadResult['thumb'];
            $row['foto_width']  = $uploadResult['width'];
            $row['foto_height'] = $uploadResult['height'];
        }

        if ($this->produkModel->update($id, $row)) {
            $this->setFlash('success', 'Produk berhasil diperbarui.');
            $this->redirect('/admin/produk');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan sistem.');
            $this->redirect('/admin/produk/' . $id . '/edit');
        }
    }

    /** POST /admin/produk/{id}/delete */
    public function destroy(string $id): void
    {
        $id = (int)$id;

        if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('/admin/produk');
        }

        if (!$this->produkModel->findById($id)) {
            $this->setFlash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/produk');
        }

        if ($this->produkModel->delete($id)) {
            $this->setFlash('success', 'Produk berhasil dihapus.');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan sistem.');
        }

        $this->redirect('/admin/produk');
    }

    /* ------------------------------------------------------------------ */

    /** Ambil dan sanitasi input POST untuk produk. */
    private function extractPostData(): array
    {
        return [
            'kategori_id' => (int)($_POST['kategori_id'] ?? 0),
            'nama'        => trim($_POST['nama'] ?? ''),
            'deskripsi'   => trim($_POST['deskripsi'] ?? ''),
            'harga'       => max(0, (int)($_POST['harga'] ?? 0)),
            'berat'       => max(0, (int)($_POST['berat'] ?? 0)),
            'stok'        => max(0, (int)($_POST['stok'] ?? 0)),
            'aktif'       => isset($_POST['aktif']) ? 1 : 0,
        ];
    }

    /** Validasi field produk, return pesan error pertama atau null. */
    private function validateProduk(array $data): ?string
    {
        if (strlen($data['nama']) < 2 || strlen($data['nama']) > 150) {
            return 'Nama produk harus antara 2 hingga 150 karakter.';
        }
        if ($data['kategori_id'] <= 0) {
            return 'Kategori wajib dipilih.';
        }
        if ($data['harga'] < 0) {
            return 'Harga tidak boleh negatif.';
        }
        if ($data['berat'] <= 0) {
            return 'Berat harus lebih dari 0 gram.';
        }
        if ($data['stok'] < 0) {
            return 'Stok tidak boleh negatif.';
        }
        if ($data['deskripsi'] !== '' && strlen($data['deskripsi']) > 2000) {
            return 'Deskripsi maksimal 2000 karakter.';
        }
        return null;
    }
}
