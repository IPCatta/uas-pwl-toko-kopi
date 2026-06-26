<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>404 — Halaman Tidak Ditemukan</title>
    <style>
        :root { --canvas-cream: #fbfbf5; --ink: #000000; --shade-50: #71717a; }
        body { font-family: 'Inter', sans-serif; background-color: var(--canvas-cream); color: var(--ink); text-align: center; padding: 50px; }
        h1 { font-size: 96px; font-weight: 330; }
        a { display: inline-block; background: var(--ink); color: #fff; padding: 12px 24px; border-radius: 9999px; text-decoration: none; }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>Halaman tidak ditemukan.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">Kembali ke Beranda</a>
</body>
</html>
