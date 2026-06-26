<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Kedai Kopi</title>
    <style>
        :root { --canvas: #F8F5F2; --ink: #1C1C1C; }
        body { background-color: var(--canvas); color: var(--ink); font-family: 'Inter', sans-serif; margin: 0; padding: 2rem; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; background: #eee; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        button { padding: 0.75rem 1.5rem; background: var(--ink); color: #fff; border: none; border-radius: 9999px; cursor: pointer; }
        .alert { padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert-error { background: #fee2e2; color: #b91c1c; }
        .alert-success { background: #dcfce3; color: #15803d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Profil Akun</h2>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= BASE_URL ?>/admin" style="color: var(--ink);">Kembali ke Dashboard</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/" style="color: var(--ink);">Kembali ke Beranda</a>
            <?php endif; ?>
        </div>

        <?php $flash = $this->getFlash(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 2rem;">
            <?php if (!empty($user['foto_thumb'])): ?>
                <img src="<?= BASE_URL . $user['foto_thumb'] ?>" alt="Foto Profil" class="profile-img">
            <?php else: ?>
                <div class="profile-img" style="display:inline-flex; align-items:center; justify-content:center; color:#999;">No Image</div>
            <?php endif; ?>
            
            <form action="<?= BASE_URL ?>/akun/profil/foto" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="form-group">
                    <input type="file" name="foto" accept="image/jpeg,image/png" required>
                </div>
                <button type="submit">Upload Foto</button>
            </form>
        </div>

        <div>
            <p><strong>Nama:</strong> <?= htmlspecialchars($user['nama'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($user['role'] ?? '') ?></p>
            <p><strong>Terdaftar:</strong> <?= htmlspecialchars(date('d-m-Y H:i', strtotime($user['created_at'] ?? 'now'))) ?></p>
        </div>
    </div>
</body>
</html>
