<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | Kedai Kopi</title>
    <style>
        /* Token dari design.md (cream/transactional track) */
        :root {
            --canvas-cream: #fbfbf5;
            --ink: #000000;
            --shade-50: #71717a;
            --aloe-10: #c1fbd4;
            --rounded-pill: 9999px;
            --rounded-lg: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            font-feature-settings: "ss03";
            background-color: var(--canvas-cream);
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .error-container {
            text-align: center;
            max-width: 480px;
        }

        .error-code {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            font-size: 96px;
            font-weight: 330;
            line-height: 1;
            letter-spacing: 2.4px;
            color: var(--ink);
            margin-bottom: 16px;
        }

        .error-title {
            font-size: 24px;
            font-weight: 500;
            line-height: 1.28;
            letter-spacing: 0.36px;
            margin-bottom: 12px;
        }

        .error-message {
            font-size: 16px;
            font-weight: 420;
            line-height: 1.5;
            color: var(--shade-50);
            margin-bottom: 32px;
        }

        .error-link {
            display: inline-block;
            background-color: var(--ink);
            color: #ffffff;
            font-size: 16px;
            font-weight: 420;
            line-height: 1.5;
            padding: 12px 24px;
            border-radius: var(--rounded-pill);
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .error-link:hover {
            background-color: #3f3f46;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-message">
            Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.
        </p>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="error-link">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
