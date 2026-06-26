<?php
class ProdukModel extends Model
{
    private const PER_PAGE = 10;

    /* ------------------------------------------------------------------ */
    /* Admin                                                                */
    /* ------------------------------------------------------------------ */

    /** Semua produk untuk halaman admin (paginated, join kategori). */
    public function getAll(int $page = 1): array
    {
        $db     = $this->getDb();
        $offset = ($page - 1) * self::PER_PAGE;
        $limit  = self::PER_PAGE;
        $stmt   = $db->prepare("
            SELECT p.*, k.nama AS nama_kategori
            FROM produk p
            JOIN kategori k ON k.id = p.kategori_id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(): int
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM produk");
        $stmt->execute();
        return (int)($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    }

    /** Satu produk by ID (join kategori). */
    public function findById(int $id): ?array
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("
            SELECT p.*, k.nama AS nama_kategori
            FROM produk p
            JOIN kategori k ON k.id = p.kategori_id
            WHERE p.id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /** Insert produk baru. $data harus mengandung semua kolom wajib. */
    public function create(array $data): bool
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("
            INSERT INTO produk
              (kategori_id, nama, deskripsi, harga, berat, stok,
               foto, foto_resized, foto_thumb, foto_width, foto_height, aktif)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "issiiisssiii",
            $data['kategori_id'],
            $data['nama'],
            $data['deskripsi'],
            $data['harga'],
            $data['berat'],
            $data['stok'],
            $data['foto'],
            $data['foto_resized'],
            $data['foto_thumb'],
            $data['foto_width'],
            $data['foto_height'],
            $data['aktif']
        );
        return $stmt->execute();
    }

    /**
     * Update produk.
     * Bila $data mengandung key 'foto' (tidak null), update kolom foto sekaligus.
     */
    public function update(int $id, array $data): bool
    {
        $db = $this->getDb();

        if (!empty($data['foto'])) {
            $stmt = $db->prepare("
                UPDATE produk
                SET kategori_id=?, nama=?, deskripsi=?, harga=?, berat=?, stok=?,
                    foto=?, foto_resized=?, foto_thumb=?, foto_width=?, foto_height=?, aktif=?
                WHERE id=?
            ");
            $stmt->bind_param(
                "issiiisssiiii",
                $data['kategori_id'],
                $data['nama'],
                $data['deskripsi'],
                $data['harga'],
                $data['berat'],
                $data['stok'],
                $data['foto'],
                $data['foto_resized'],
                $data['foto_thumb'],
                $data['foto_width'],
                $data['foto_height'],
                $data['aktif'],
                $id
            );
        } else {
            $stmt = $db->prepare("
                UPDATE produk
                SET kategori_id=?, nama=?, deskripsi=?, harga=?, berat=?, stok=?, aktif=?
                WHERE id=?
            ");
            $stmt->bind_param(
                "issiiiii",
                $data['kategori_id'],
                $data['nama'],
                $data['deskripsi'],
                $data['harga'],
                $data['berat'],
                $data['stok'],
                $data['aktif'],
                $id
            );
        }

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* ------------------------------------------------------------------ */
    /* Katalog Publik                                                        */
    /* ------------------------------------------------------------------ */

    /**
     * Produk aktif untuk halaman katalog (paginated, optional filter).
     */
    public function getAktif(int $page = 1, int $kategoriId = 0, string $q = ''): array
    {
        $db     = $this->getDb();
        $limit  = self::PER_PAGE;
        $offset = ($page - 1) * $limit;

        [$where, $types, $params] = $this->buildAktifWhere($kategoriId, $q);

        $params[] = $limit;
        $params[] = $offset;
        $types   .= "ii";

        $stmt = $db->prepare("
            SELECT p.*, k.nama AS nama_kategori
            FROM produk p
            JOIN kategori k ON k.id = p.kategori_id
            WHERE $where
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countAktif(int $kategoriId = 0, string $q = ''): int
    {
        $db = $this->getDb();

        [$where, $types, $params] = $this->buildAktifWhere($kategoriId, $q);

        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM produk p WHERE $where");

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return (int)($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    }

    /* ------------------------------------------------------------------ */
    /* API Endpoint                                                          */
    /* ------------------------------------------------------------------ */

    /**
     * Cari produk aktif untuk endpoint /api/produk.
     * Kembalikan kolom minimal sesuai kontrak IMPLEMENTATION §4.
     */
    public function searchForApi(string $q = '', int $kategoriId = 0): array
    {
        $db = $this->getDb();

        [$where, $types, $params] = $this->buildAktifWhere($kategoriId, $q);

        $stmt = $db->prepare("
            SELECT p.id, p.nama, p.harga, p.stok, p.foto_thumb
            FROM produk p
            WHERE $where
            ORDER BY p.nama ASC
            LIMIT 50
        ");

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* ------------------------------------------------------------------ */

    /**
     * Bangun klausa WHERE + types + params untuk query produk aktif.
     * Menghindari duplikasi antara getAktif, countAktif, searchForApi.
     *
     * @return array [whereString, typeString, paramsArray]
     */
    private function buildAktifWhere(int $kategoriId, string $q): array
    {
        $conditions = ["p.aktif = 1"];
        $types      = "";
        $params     = [];

        if ($kategoriId > 0) {
            $conditions[] = "p.kategori_id = ?";
            $types       .= "i";
            $params[]     = $kategoriId;
        }

        if ($q !== '') {
            $conditions[] = "p.nama LIKE ?";
            $types       .= "s";
            $params[]     = '%' . $q . '%';
        }

        return [implode(' AND ', $conditions), $types, $params];
    }
}
