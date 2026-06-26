<?php
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', __DIR__);
define('BASE_URL', 'http://localhost/kedai-kopi/public');

require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Model.php';

if (file_exists(CONFIG_PATH . '/database.php')) {
    $db = require_once CONFIG_PATH . '/database.php';
}

$router = new Router();

$router->get('/', 'KatalogController@index');
$router->get('/produk/{id}', 'KatalogController@detail');
$router->get('/register', 'AuthController@formRegister');
$router->post('/register', 'AuthController@register');
$router->get('/login', 'AuthController@formLogin');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');
$router->get('/keranjang', 'CheckoutController@keranjang');
$router->post('/keranjang/tambah', 'CheckoutController@tambah');
$router->post('/keranjang/ubah', 'CheckoutController@ubah');
$router->get('/checkout', 'CheckoutController@form');
$router->post('/checkout', 'CheckoutController@simpan');
$router->get('/admin', 'DashboardController@index');
$router->get('/admin/kategori', 'KategoriController@index');
$router->post('/admin/kategori', 'KategoriController@store');
$router->post('/admin/kategori/{id}/update', 'KategoriController@update');
$router->post('/admin/kategori/{id}/delete', 'KategoriController@destroy');
$router->get('/admin/produk', 'ProdukController@index');
$router->post('/admin/produk', 'ProdukController@store');
$router->post('/admin/produk/{id}/update', 'ProdukController@update');
$router->post('/admin/produk/{id}/delete', 'ProdukController@destroy');
$router->get('/admin/transaksi', 'TransaksiController@index');
$router->get('/admin/transaksi/{id}', 'TransaksiController@detail');
$router->post('/admin/transaksi/{id}/status', 'TransaksiController@updateStatus');
$router->get('/admin/laporan', 'LaporanController@index');
$router->get('/admin/laporan/excel', 'LaporanController@exportExcel');
$router->get('/admin/laporan/pdf', 'LaporanController@exportPdf');
$router->get('/akun/profil', 'AuthController@profil');
$router->post('/akun/profil/foto', 'AuthController@uploadFoto');
$router->get('/api/wilayah/kota', 'api/WilayahApiController@kota');
$router->get('/api/wilayah/kecamatan', 'api/WilayahApiController@kecamatan');
$router->post('/api/ongkir', 'api/OngkirApiController@hitung');
$router->get('/api/produk', 'api/ProdukApiController@cari');

$router->dispatch();
