<?php
require_once APP_PATH . '/models/ProdukModel.php';
require_once APP_PATH . '/models/KategoriModel.php';

class KatalogController extends Controller
{
    private ProdukModel   $produkModel;
    private KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->produkModel   = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
    }

    /** GET / */
    public function index(): void
    {
        $q          = trim($_GET['q']       ?? '');
        $kategoriId = max(0, (int)($_GET['kategori'] ?? 0));
        $page       = max(1, (int)($_GET['page']     ?? 1));

        $totalItems = $this->produkModel->countAktif($kategoriId, $q);
        $totalPages = (int)ceil($totalItems / 10) ?: 1;
        $page       = min($page, $totalPages);

        $this->view('katalog/index', [
            'title'          => 'Katalog Kopi',
            'produks'        => $this->produkModel->getAktif($page, $kategoriId, $q),
            'kategoris'      => $this->kategoriModel->getAll(),
            'q'              => $q,
            'currentKategori'=> $kategoriId,
            'page'           => $page,
            'totalPages'     => $totalPages,
        ]);
    }

    /** GET /produk/{id} */
    public function detail(string $id): void
    {
        $produk = $this->produkModel->findById((int)$id);

        if (!$produk || !$produk['aktif']) {
            http_response_code(404);
            require APP_PATH . '/views/errors/404.php';
            return;
        }

        $this->view('katalog/detail', [
            'title'  => htmlspecialchars($produk['nama']) . ' — Kedai Kopi',
            'produk' => $produk,
        ]);
    }
}
