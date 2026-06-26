<?php
class DashboardModel extends Model
{
    public function getStats(): array
    {
        $db = $this->getDb();
        $stats = [
            'total_produk' => 0,
            'total_transaksi' => 0,
            'total_penjualan' => 0
        ];
        
        $res = $db->query("SELECT COUNT(*) as c FROM produk");
        if ($res && $row = $res->fetch_assoc()) {
            $stats['total_produk'] = $row['c'];
        }

        $res = $db->query("SELECT COUNT(*) as c FROM transaksi WHERE status != 'batal'");
        if ($res && $row = $res->fetch_assoc()) {
            $stats['total_transaksi'] = $row['c'];
        }

        $res = $db->query("SELECT SUM(total) as s FROM transaksi WHERE status IN ('dibayar', 'dikirim', 'selesai')");
        if ($res && $row = $res->fetch_assoc()) {
            $stats['total_penjualan'] = $row['s'] ?? 0;
        }

        return $stats;
    }

    public function getRecentTransactions(int $limit = 5): array
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT kode_transaksi, nama_penerima, total, status, created_at FROM transaksi ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
