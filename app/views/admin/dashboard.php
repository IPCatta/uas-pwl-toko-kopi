<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Kedai Kopi</title>
    <style>
        :root {
            --canvas: #F8F5F2;
            --ink: #1C1C1C;
        }
        body {
            background-color: var(--canvas);
            color: var(--ink);
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
            color: #666;
        }
        .stat-card .value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .recent-section {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            font-weight: 600;
            color: #666;
        }
        .btn-logout {
            background: none;
            border: none;
            color: var(--ink);
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Dashboard Admin</h2>
            <div>
                <a href="<?= BASE_URL ?>/akun/profil" style="margin-right: 15px; color: var(--ink);">Profil</a>
                <form action="<?= BASE_URL ?>/logout" method="POST" style="display: inline;">
                    <?php require_once APP_PATH . '/helpers/CsrfHelper.php'; ?>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(CsrfHelper::generateToken()) ?>">
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Produk</h3>
                <div class="value"><?= number_format($stats['total_produk'], 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Transaksi</h3>
                <div class="value"><?= number_format($stats['total_transaksi'], 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Penjualan</h3>
                <div class="value">Rp <?= number_format($stats['total_penjualan'], 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="recent-section">
            <h3>5 Transaksi Terbaru</h3>
            <?php if (empty($recent)): ?>
                <p>Belum ada transaksi.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Nama Penerima</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $trx): ?>
                            <tr>
                                <td><?= htmlspecialchars($trx['kode_transaksi']) ?></td>
                                <td><?= htmlspecialchars($trx['nama_penerima']) ?></td>
                                <td>Rp <?= number_format($trx['total'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($trx['status']) ?></td>
                                <td><?= htmlspecialchars(date('d-m-Y H:i', strtotime($trx['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
