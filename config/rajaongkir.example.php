<?php
/**
 * Konfigurasi RajaOngkir API V2 — kedai-kopi
 *
 * SALIN file ini menjadi config/rajaongkir.php, lalu isi dengan API key Anda.
 * File config/rajaongkir.php sudah di-.gitignore (tidak di-commit).
 *
 * [SET] Isi api_key dan origin_id.
 */

return [
    'base_url'  => 'https://rajaongkir.komerce.id/api/v1',
    'api_key'   => '',       // [SET] API key dari dashboard RajaOngkir
    'origin_id' => 0,        // [SET] id kecamatan lokasi roastery
    'couriers'  => 'jne:jnt:sicepat:pos:tiki',
    'price'     => 'lowest',
];
