<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
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
            <a href="<?= BASE_URL ?>/login"    class="btn btn-outline btn-sm">Masuk</a>
            <a href="<?= BASE_URL ?>/register"  class="btn btn-primary btn-sm">Daftar</a>
        <?php endif; ?>
    </div>
</nav>

<div class="produk-detail-wrap">

    <a href="<?= BASE_URL ?>/" class="back-link">&#8592; Kembali ke Katalog</a>

    <div class="produk-detail">

        <!-- Foto -->
        <div class="produk-detail__img">
            <?php if (!empty($produk['foto_resized'])): ?>
                <img src="<?= BASE_URL . htmlspecialchars($produk['foto_resized']) ?>"
                     alt="<?= htmlspecialchars($produk['nama']) ?>">
            <?php elseif (!empty($produk['foto'])): ?>
                <img src="<?= BASE_URL . htmlspecialchars($produk['foto']) ?>"
                     alt="<?= htmlspecialchars($produk['nama']) ?>">
            <?php else: ?>
                <div class="produk-detail__img-placeholder">Belum ada foto</div>
            <?php endif; ?>
        </div>

        <!-- Meta -->
        <div class="produk-detail__meta">
            <div class="produk-detail__category">
                <?= htmlspecialchars($produk['nama_kategori']) ?>
            </div>

            <h1 class="produk-detail__name"><?= htmlspecialchars($produk['nama']) ?></h1>

            <p class="produk-detail__price">
                Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
            </p>

            <div class="produk-detail__info">
                <p>Berat: <?= number_format($produk['berat'], 0, ',', '.') ?> gram</p>
                <?php if ((int)$produk['stok'] > 0): ?>
                    <p>Stok: <span class="badge badge-mint"><?= (int)$produk['stok'] ?> tersedia</span></p>
                <?php else: ?>
                    <p><span class="badge badge-shade">Stok Habis</span></p>
                <?php endif; ?>
            </div>

            <!-- Add to cart — handler diimplementasikan Orang 3 -->
            <?php if ((int)$produk['stok'] > 0): ?>
                <?php require_once APP_PATH . '/helpers/CsrfHelper.php'; ?>
                <form method="POST" action="<?= BASE_URL ?>/keranjang/tambah">
                    <input type="hidden" name="csrf_token"
                           value="<?= htmlspecialchars(CsrfHelper::generateToken()) ?>">
                    <input type="hidden" name="produk_id" value="<?= (int)$produk['id'] ?>">
                    <div style="display:flex;align-items:center;gap:var(--sp-md);margin-bottom:var(--sp-md)">
                        <label for="jumlah" style="font-size:14px;font-weight:500">Jumlah</label>
                        <input id="jumlah" name="jumlah" type="number"
                               class="form-control" style="width:80px"
                               value="1" min="1" max="<?= (int)$produk['stok'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Tambah ke Keranjang</button>
                </form>
            <?php else: ?>
                <button class="btn btn-outline btn-full" disabled
                        style="opacity:.5;cursor:not-allowed">Stok Habis</button>
            <?php endif; ?>

            <?php if (!empty($produk['deskripsi'])): ?>
                <div class="produk-detail__desc">
                    <?= nl2br(htmlspecialchars($produk['deskripsi'])) ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
