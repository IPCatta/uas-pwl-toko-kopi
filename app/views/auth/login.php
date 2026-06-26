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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background: var(--ink);
            color: #fff;
            border: none;
            border-radius: 9999px; /* pill shape */
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
        }
        button:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
        }
        .alert-success {
            background: #dcfce3;
            color: #15803d;
        }
        .text-center { text-align: center; }
        a { color: var(--ink); }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin-top: 0;">Login</h2>
        
        <?php $flash = $this->getFlash(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>
        <p class="text-center" style="margin-top: 1.5rem; font-size: 0.9rem;">
            Belum punya akun? <a href="<?= BASE_URL ?>/register">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
