<?php
require_once APP_PATH . '/models/ProdukModel.php';

/**
 * GET /api/produk?q=STRING&kategori=INT
 *
 * Kontrak response (IMPLEMENTATION.md §4):
 *   { "success": true, "data": [
 *       { "id", "nama", "harga", "stok", "foto_thumb" }
 *   ]}
 */
class ProdukApiController extends Controller
{
    public function cari(): void
    {
        $q          = trim($_GET['q']       ?? '');
        $kategoriId = max(0, (int)($_GET['kategori'] ?? 0));

        $produkModel = new ProdukModel();
        $data        = $produkModel->searchForApi($q, $kategoriId);

        $this->json(['success' => true, 'data' => $data]);
    }
}
