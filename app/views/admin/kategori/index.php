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

    <?php $activeNav = 'kategori'; require APP_PATH . '/views/partials/admin-nav.php'; ?>

    <main class="admin-main">

        <!-- Flash message -->
        <?php $flash = $this->getFlash(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Add / Edit form panel -->
        <div class="form-panel">
            <h2 id="form-title">Tambah Kategori</h2>

            <form id="kategori-form" method="POST" action="<?= BASE_URL ?>/admin/kategori">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">Nama Kategori <span style="color:#b91c1c">*</span></label>
                        <input id="nama" name="nama" type="text" class="form-control"
                               placeholder="cth. Arabika, Robusta…" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <input id="deskripsi" name="deskripsi" type="text" class="form-control"
                               placeholder="Opsional" maxlength="1000">
                    </div>
                </div>

                <div class="form-actions">
                    <button id="btn-submit" type="submit" class="btn btn-primary">Simpan</button>
                    <button id="btn-cancel" type="button" class="btn btn-outline"
                            style="display:none" onclick="resetKategoriForm()">Batal</button>
                </div>
            </form>
        </div>

        <!-- Category table -->
        <div class="card card-sm">
            <div class="page-header" style="margin-bottom:var(--sp-lg)">
                <div class="page-header__text">
                    <h1>Kategori</h1>
                    <p><?= count($kategoris) ?> kategori terdaftar</p>
                </div>
            </div>

            <?php if (empty($kategoris)): ?>
                <div class="empty-state">
                    <h3>Belum ada kategori</h3>
                    <p>Tambahkan kategori pertama menggunakan form di atas.</p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Produk</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($kategoris as $k): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
                                <td style="color:var(--c-shade-50)">
                                    <?= htmlspecialchars($k['deskripsi'] ?? '—') ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $k['jumlah_produk'] > 0 ? 'mint' : 'shade' ?>">
                                        <?= (int)$k['jumlah_produk'] ?> produk
                                    </span>
                                </td>
                                <td style="color:var(--c-shade-50)">
                                    <?= date('d-m-Y', strtotime($k['created_at'])) ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline btn-sm"
                                        onclick="editKategori(
                                            <?= (int)$k['id'] ?>,
                                            <?= json_encode($k['nama']) ?>,
                                            <?= json_encode($k['deskripsi'] ?? '') ?>
                                        )">Edit</button>

                                    <?php if ((int)$k['jumlah_produk'] === 0): ?>
                                        <form method="POST"
                                              action="<?= BASE_URL ?>/admin/kategori/<?= (int)$k['id'] ?>/delete"
                                              style="display:inline"
                                              onsubmit="return confirm('Hapus kategori ini?')">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= htmlspecialchars($csrf_token) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm"
                                                style="cursor:not-allowed;opacity:.4"
                                                title="Tidak dapat dihapus — masih digunakan oleh produk"
                                                disabled>Hapus</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function editKategori(id, nama, deskripsi) {
    document.getElementById('kategori-form').action = '<?= BASE_URL ?>/admin/kategori/' + id + '/update';
    document.getElementById('nama').value      = nama;
    document.getElementById('deskripsi').value = deskripsi;
    document.getElementById('form-title').textContent      = 'Edit Kategori';
    document.getElementById('btn-submit').textContent      = 'Simpan Perubahan';
    document.getElementById('btn-cancel').style.display    = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetKategoriForm() {
    document.getElementById('kategori-form').action        = '<?= BASE_URL ?>/admin/kategori';
    document.getElementById('nama').value                  = '';
    document.getElementById('deskripsi').value             = '';
    document.getElementById('form-title').textContent      = 'Tambah Kategori';
    document.getElementById('btn-submit').textContent      = 'Simpan';
    document.getElementById('btn-cancel').style.display    = 'none';
}
</script>
</body>
</html>
