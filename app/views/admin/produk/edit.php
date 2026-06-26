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

        <a href="<?= BASE_URL ?>/admin/produk" class="back-link">&#8592; Kembali ke Daftar Produk</a>

        <?php $flash = $this->getFlash(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <div class="page-header__text">
                <h1>Edit Produk</h1>
                <p><?= htmlspecialchars($produk['nama']) ?></p>
            </div>
        </div>

        <div class="card">
            <form method="POST"
                  action="<?= BASE_URL ?>/admin/produk/<?= (int)$produk['id'] ?>/update"
                  enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-row">
                    <div class="form-group" style="grid-column: 1 / -1">
                        <label for="nama">Nama Produk <span style="color:#b91c1c">*</span></label>
                        <input id="nama" name="nama" type="text" class="form-control"
                               value="<?= htmlspecialchars($produk['nama']) ?>"
                               maxlength="150" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="kategori_id">Kategori <span style="color:#b91c1c">*</span></label>
                        <select id="kategori_id" name="kategori_id" class="form-control" required>
                            <option value="">— Pilih Kategori —</option>
                            <?php foreach ($kategoris as $k): ?>
                                <option value="<?= (int)$k['id'] ?>"
                                    <?= ((int)$k['id'] === (int)$produk['kategori_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga (Rp) <span style="color:#b91c1c">*</span></label>
                        <input id="harga" name="harga" type="number" class="form-control"
                               value="<?= (int)$produk['harga'] ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="berat">Berat (gram) <span style="color:#b91c1c">*</span></label>
                        <input id="berat" name="berat" type="number" class="form-control"
                               value="<?= (int)$produk['berat'] ?>" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok <span style="color:#b91c1c">*</span></label>
                        <input id="stok" name="stok" type="number" class="form-control"
                               value="<?= (int)$produk['stok'] ?>" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control"
                              maxlength="2000"><?= htmlspecialchars($produk['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="foto">Ganti Foto (opsional)</label>

                    <?php if (!empty($produk['foto_thumb'])): ?>
                        <div class="upload-current">
                            <img src="<?= BASE_URL . htmlspecialchars($produk['foto_thumb']) ?>"
                                 alt="Foto saat ini">
                            <span style="font-size:13px;color:var(--c-shade-50)">Foto saat ini</span>
                        </div>
                    <?php endif; ?>

                    <input id="foto" name="foto" type="file" class="form-control"
                           accept="image/jpeg,image/png"
                           style="margin-top:var(--sp-sm)"
                           onchange="previewFoto(this)">
                    <p class="form-hint">Biarkan kosong untuk mempertahankan foto yang ada. JPG/PNG, maks. 2 MB.</p>

                    <div id="foto-preview" class="upload-current" style="display:none">
                        <img id="foto-preview-img" src="" alt="Preview baru">
                        <span style="font-size:13px;color:var(--c-shade-50)">Preview foto baru</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="toggle-wrap">
                        <input id="aktif" name="aktif" type="checkbox" value="1"
                               <?= $produk['aktif'] ? 'checked' : '' ?>>
                        <label for="aktif" style="margin:0">Produk aktif (tampil di katalog)</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="<?= BASE_URL ?>/admin/produk" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function previewFoto(input) {
    var preview = document.getElementById('foto-preview');
    var img     = document.getElementById('foto-preview-img');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            preview.style.display = 'flex';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
