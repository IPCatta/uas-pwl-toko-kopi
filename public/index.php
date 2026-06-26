<?php
/**
 * Front Controller — kedai-kopi
 *
 * Semua HTTP request masuk lewat sini.
 * Memuat konfigurasi, mendefinisikan rute, lalu menyerahkan ke Router.
 */

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ============================================================
// Konfigurasi Session
// ============================================================
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 7200); // 120 menit

session_set_cookie_params([
    'lifetime' => 0,
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek timeout (120 menit idle)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// Definisi path dasar
define('BASE_PATH', dirname(__DIR__));          // root proyek (kedai-kopi/)
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', __DIR__);                 // kedai-kopi/public/

// BASE_URL — sesuaikan dengan environment
// [SET] Ganti sesuai environment lokal
define('BASE_URL', 'http://localhost/kedai-kopi/public');

// Autoload core classes
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Model.php';

// Muat koneksi database (bila file config ada)
if (file_exists(CONFIG_PATH . '/database.php')) {
    $db = require_once CONFIG_PATH . '/database.php';
}

// ============================================================
// Definisi Rute
// ============================================================
$router = new Router();

// --- Halaman publik (katalog) ---
$router->get('/', 'KatalogController@index');
$router->get('/produk/{id}', 'KatalogController@detail');

// --- Auth ---
$router->get('/register', 'AuthController@formRegister');
$router->post('/register', 'AuthController@register');
$router->get('/login', 'AuthController@formLogin');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');

// --- Keranjang ---
$router->get('/keranjang', 'CheckoutController@keranjang');
$router->post('/keranjang/tambah', 'CheckoutController@tambah');
$router->post('/keranjang/ubah', 'CheckoutController@ubah');

// --- Checkout ---
$router->get('/checkout', 'CheckoutController@form');
$router->post('/checkout', 'CheckoutController@simpan');

// --- Admin: Dashboard ---
$router->get('/admin', 'DashboardController@index');

// --- Admin: Kategori ---
$router->get('/admin/kategori', 'KategoriController@index');
$router->post('/admin/kategori', 'KategoriController@store');
$router->post('/admin/kategori/{id}/update', 'KategoriController@update');
$router->post('/admin/kategori/{id}/delete', 'KategoriController@destroy');

// --- Admin: Produk ---
$router->get('/admin/produk', 'ProdukController@index');
$router->post('/admin/produk', 'ProdukController@store');
$router->post('/admin/produk/{id}/update', 'ProdukController@update');
$router->post('/admin/produk/{id}/delete', 'ProdukController@destroy');

// --- Admin: Transaksi ---
$router->get('/admin/transaksi', 'TransaksiController@index');
$router->get('/admin/transaksi/{id}', 'TransaksiController@detail');
$router->post('/admin/transaksi/{id}/status', 'TransaksiController@updateStatus');

// --- Admin: Laporan ---
$router->get('/admin/laporan', 'LaporanController@index');
$router->get('/admin/laporan/excel', 'LaporanController@exportExcel');
$router->get('/admin/laporan/pdf', 'LaporanController@exportPdf');

// --- Akun: Profil ---
$router->get('/akun/profil', 'AuthController@profil');
$router->post('/akun/profil/foto', 'AuthController@uploadFoto');

// --- API Internal (JSON) ---
$router->get('/api/wilayah/kota', 'api/WilayahApiController@kota');
$router->get('/api/wilayah/kecamatan', 'api/WilayahApiController@kecamatan');
$router->post('/api/ongkir', 'api/OngkirApiController@hitung');
$router->get('/api/produk', 'api/ProdukApiController@cari');

// ============================================================
// Dispatch — jalankan router
// ============================================================
$router->dispatch();
