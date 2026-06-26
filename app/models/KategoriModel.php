<?php
class KategoriModel extends Model
{
    /** Semua kategori + jumlah produk terkait. */
    public function getAll(): array
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("
            SELECT k.*, COUNT(p.id) AS jumlah_produk
            FROM kategori k
            LEFT JOIN produk p ON p.kategori_id = k.id
            GROUP BY k.id
            ORDER BY k.nama ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Ambil satu kategori by ID. */
    public function findById(int $id): ?array
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM kategori WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Cek duplikasi nama (opsional kecualikan ID saat update).
     */
    public function findByNama(string $nama, int $excludeId = 0): ?array
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("SELECT id FROM kategori WHERE nama = ? AND id != ? LIMIT 1");
        $stmt->bind_param("si", $nama, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function create(array $data): bool
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("INSERT INTO kategori (nama, deskripsi) VALUES (?, ?)");
        $stmt->bind_param("ss", $data['nama'], $data['deskripsi']);
        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("UPDATE kategori SET nama = ?, deskripsi = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['nama'], $data['deskripsi'], $id);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("DELETE FROM kategori WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /** Cek apakah kategori dipakai oleh setidaknya satu produk (tolak hapus jika true). */
    public function isUsedByProducts(int $id): bool
    {
        $db   = $this->getDb();
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM produk WHERE kategori_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return (int)($stmt->get_result()->fetch_assoc()['cnt'] ?? 0) > 0;
    }
}
