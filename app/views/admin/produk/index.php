<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — Kedai Kopi</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body>
<div class="admin-wrap">

    <?php $activeNav = 'produk'; require APP_PATH . '/views/partials/admin-nav.php'; ?>

    <main class="admin-main">

        <!-- Flash message -->
        <?php $flash = $this->getFlash(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <div class="page-header__text">
                <h1>Produk</h1>
                <p><?= number_format($totalPages * 10, 0, ',', '.') ?> maks — halaman <?= $page ?> / <?= $totalPages ?></p>
            </div>
            <a href="<?= BASE_URL ?>/admin/produk/tambah" class="btn btn-primary">+ Tambah Produk</a>
        </div>

        <div class="card card-sm">
            <?php if (empty($produks)): ?>
                <div class="empty-state">
                    <h3>Belum ada produk</h3>
                    <p><a href="<?= BASE_URL ?>/admin/produk/tambah">Tambahkan produk pertama</a></p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:56px"></th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Berat</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($produks as $p): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($p['foto_thumb'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($p['foto_thumb']) ?>"
                                             alt="<?= htmlspecialchars($p['nama']) ?>"
                                             class="table-thumb">
                                    <?php else: ?>
                                        <div class="table-thumb"
                                             style="background:var(--c-canvas-cream);border:1px solid var(--c-hairline-light);border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;color:var(--c-shade-40);font-size:11px;">
                                            —
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($p['nama']) ?></strong></td>
                                <td style="color:var(--c-shade-50)"><?= htmlspecialchars($p['nama_kategori']) ?></td>
                                <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                <td><?= number_format($p['berat'], 0, ',', '.') ?> g</td>
                                <td>
                                    <span class="badge badge-<?= (int)$p['stok'] > 0 ? 'mint' : 'shade' ?>">
                                        <?= (int)$p['stok'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $p['aktif'] ? 'aktif' : 'nonaktif' ?>">
                                        <?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/produk/<?= (int)$p['id'] ?>/edit"
                                       class="btn btn-outline btn-sm">Edit</a>

                                    <form method="POST"
                                          action="<?= BASE_URL ?>/admin/produk/<?= (int)$p['id'] ?>/delete"
                                          style="display:inline"
                                          onsubmit="return confirm('Hapus produk ini?')">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= htmlspecialchars($csrf_token) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">&lsaquo;</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>">&rsaquo;</a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
