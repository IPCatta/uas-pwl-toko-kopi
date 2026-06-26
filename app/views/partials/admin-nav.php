<?php
/**
 * Partial: admin top navigation bar
 * Dipakai oleh: admin/kategori, admin/produk, admin/dashboard, dsb.
 * Variabel yang diharapkan dari scope view:
 *   $activeNav (string) — 'dashboard', 'kategori', 'produk', 'transaksi', 'laporan'
 */
if (!isset($activeNav)) $activeNav = '';
?>
<nav class="admin-topnav">
    <a class="admin-topnav__brand" href="<?= BASE_URL ?>/admin">&#9749; Kedai Kopi Admin</a>

    <ul class="admin-topnav__nav">
        <li><a href="<?= BASE_URL ?>/admin"            class="<?= $activeNav === 'dashboard'  ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/admin/kategori"   class="<?= $activeNav === 'kategori'   ? 'active' : '' ?>">Kategori</a></li>
        <li><a href="<?= BASE_URL ?>/admin/produk"     class="<?= $activeNav === 'produk'     ? 'active' : '' ?>">Produk</a></li>
        <li><a href="<?= BASE_URL ?>/admin/transaksi"  class="<?= $activeNav === 'transaksi'  ? 'active' : '' ?>">Transaksi</a></li>
        <li><a href="<?= BASE_URL ?>/admin/laporan"    class="<?= $activeNav === 'laporan'    ? 'active' : '' ?>">Laporan</a></li>
    </ul>

    <div class="admin-topnav__right">
        <a href="<?= BASE_URL ?>/akun/profil">Profil</a>
        <?php require_once APP_PATH . '/helpers/CsrfHelper.php'; ?>
        <form action="<?= BASE_URL ?>/logout" method="POST" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(CsrfHelper::generateToken()) ?>">
            <button type="submit" class="admin-topnav__logout">Logout</button>
        </form>
    </div>
</nav>
