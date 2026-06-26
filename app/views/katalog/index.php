<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — Kedai Kopi</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body>

<!-- Public nav -->
<nav class="public-topnav">
    <a class="public-topnav__brand" href="<?= BASE_URL ?>/">&#9749; Kedai Kopi</a>
    <div class="public-topnav__right">
        <a href="<?= BASE_URL ?>/keranjang" class="btn btn-outline btn-sm">Keranjang</a>
        <?php if (!empty($_SESSION['auth_user_id'])): ?>
            <a href="<?= BASE_URL ?>/akun/profil" class="btn btn-sm"
               style="color:var(--c-shade-50)">Akun</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login"  class="btn btn-outline btn-sm">Masuk</a>
            <a href="<?= BASE_URL ?>/register" class="btn btn-primary btn-sm">Daftar</a>
        <?php endif; ?>
    </div>
</nav>

<div class="katalog-wrap">

    <!-- Header + search -->
    <div class="katalog-header">
        <h1>Katalog Kopi</h1>
        <form id="search-form" class="search-bar">
            <input id="search-q" name="q" type="search" class="form-control"
                   placeholder="Cari kopi…"
                   value="<?= htmlspecialchars($q) ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>

    <!-- Category filter pills -->
    <?php if (!empty($kategoris)): ?>
    <div class="filter-bar">
        <a href="<?= BASE_URL ?>/"
           class="filter-pill<?= $currentKategori === 0 ? ' active' : '' ?>"
           data-kategori="0">Semua</a>

        <?php foreach ($kategoris as $k): ?>
            <a href="<?= BASE_URL ?>/?kategori=<?= (int)$k['id'] ?>"
               class="filter-pill<?= ((int)$k['id'] === $currentKategori) ? ' active' : '' ?>"
               data-kategori="<?= (int)$k['id'] ?>">
                <?= htmlspecialchars($k['nama']) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Product grid (initial SSR render; AJAX replaces innerHTML) -->
    <div id="product-grid" class="product-grid">
        <?php if (empty($produks)): ?>
            <div class="empty-state">
                <h3>Belum ada produk</h3>
                <p>Coba kata kunci lain atau pilih kategori berbeda.</p>
            </div>
        <?php else: ?>
            <?php foreach ($produks as $p): ?>
                <a href="<?= BASE_URL ?>/produk/<?= (int)$p['id'] ?>" class="product-card">
                    <div class="product-card__img">
                        <?php if (!empty($p['foto_thumb'])): ?>
                            <img src="<?= BASE_URL . htmlspecialchars($p['foto_thumb']) ?>"
                                 alt="<?= htmlspecialchars($p['nama']) ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="product-card__placeholder">Belum ada foto</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__category">
                            <?= htmlspecialchars($p['nama_kategori']) ?>
                        </div>
                        <div class="product-card__name"><?= htmlspecialchars($p['nama']) ?></div>
                        <div class="product-card__price">
                            Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                        </div>
                        <?php if ((int)$p['stok'] === 0): ?>
                            <span class="product-card__sold-out">Stok Habis</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- SSR Pagination (hidden when AJAX is active) -->
    <?php if ($totalPages > 1): ?>
    <nav class="pagination" id="ssr-pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(['q' => $q, 'kategori' => $currentKategori, 'page' => $page - 1]) ?>">&lsaquo;</a>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?>
                <span class="current"><?= $i ?></span>
            <?php else: ?>
                <a href="?<?= http_build_query(['q' => $q, 'kategori' => $currentKategori, 'page' => $i]) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(['q' => $q, 'kategori' => $currentKategori, 'page' => $page + 1]) ?>">&rsaquo;</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

</div>

<script>
window.BASE_URL = '<?= BASE_URL ?>';
</script>
<script src="<?= BASE_URL ?>/assets/js/katalog.js"></script>
</body>
</html>
