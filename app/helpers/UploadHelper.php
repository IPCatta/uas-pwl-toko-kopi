<?php
/**
 * UploadHelper — kedai-kopi
 *
 * Validasi, rename, resize, dan thumbnail via GD Library.
 * Dipanggil oleh ProdukController (konteks 'produk') dan
 * AuthController (konteks 'profil').
 *
 * Signature utama:
 *   UploadHelper::upload($file, $context) → array|false
 *
 * Return array:
 *   ['asli'    => '/assets/uploads/{ctx}/name.ext',
 *    'resized' => '/assets/uploads/{ctx}/resized/name.jpg',
 *    'thumb'   => '/assets/uploads/{ctx}/thumbs/name.jpg',
 *    'width'   => int,   // dimensi resized
 *    'height'  => int]
 *
 * Return false bila validasi gagal — periksa getLastError().
 */
class UploadHelper
{
    private static string $lastError = '';

    /* konstanta dari IMPLEMENTATION.md §1 */
    private const MAX_BYTES     = 2097152;   // 2 MB
    private const MAX_WIDTH     = 800;       // lebar max resized
    private const THUMB_WIDTH   = 200;       // lebar thumbnail
    private const QUALITY       = 80;        // kualitas JPEG output
    private const ALLOWED_EXT   = ['jpg', 'jpeg', 'png'];
    private const ALLOWED_MIME  = ['image/jpeg', 'image/png'];

    /* ------------------------------------------------------------------ */

    /**
     * Upload, validasi, resize, dan simpan ke folder uploads/{context}/.
     *
     * @param array  $file     Elemen dari $_FILES (mis. $_FILES['foto'])
     * @param string $context  'produk' atau 'profil'
     * @return array|false
     */
    public static function upload(array $file, string $context): array|false
    {
        self::$lastError = '';

        /* 1. Cek PHP upload error */
        if ($file['error'] !== UPLOAD_ERR_OK) {
            self::$lastError = 'Gagal mengunggah file (kode: ' . $file['error'] . ').';
            return false;
        }

        /* 2. Ukuran file */
        if ($file['size'] > self::MAX_BYTES) {
            self::$lastError = 'Ukuran file maksimal 2 MB.';
            return false;
        }

        /* 3. Ekstensi (whitelist) */
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            self::$lastError = 'Tipe file tidak diizinkan. Gunakan JPG atau PNG.';
            return false;
        }

        /* 4. MIME type */
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            self::$lastError = 'MIME type tidak valid.';
            return false;
        }

        /* 5. getimagesize — pastikan benar-benar gambar */
        $imgInfo = getimagesize($file['tmp_name']);
        if ($imgInfo === false) {
            self::$lastError = 'File bukan gambar yang valid.';
            return false;
        }

        /* 6. Siapkan direktori */
        $baseDir    = PUBLIC_PATH . '/assets/uploads/' . $context . '/';
        $resizedDir = $baseDir . 'resized/';
        $thumbsDir  = $baseDir . 'thumbs/';

        foreach ([$baseDir, $resizedDir, $thumbsDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        /* 7. Nama file unik — simpan dengan ekstensi asli */
        $newName     = uniqid() . time() . '.' . $ext;
        $originalPath = $baseDir . $newName;

        /* 8. Pindahkan file original */
        if (!move_uploaded_file($file['tmp_name'], $originalPath)) {
            self::$lastError = 'Gagal menyimpan file.';
            return false;
        }

        /* 9. Load ke GD */
        $src = ($mime === 'image/png')
            ? imagecreatefrompng($originalPath)
            : imagecreatefromjpeg($originalPath);

        if (!$src) {
            self::$lastError = 'Gagal memproses gambar.';
            return false;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        /* 10. Buat resized (maks 800px lebar) — output selalu JPEG */
        $resizedName = pathinfo($newName, PATHINFO_FILENAME) . '.jpg';
        [$rw, $rh] = self::resizeAndSave($src, $origW, $origH, self::MAX_WIDTH, $resizedDir . $resizedName);

        /* 11. Buat thumbnail (200px lebar) */
        self::resizeAndSave($src, $origW, $origH, self::THUMB_WIDTH, $thumbsDir . $resizedName);

        imagedestroy($src);

        $urlBase = '/assets/uploads/' . $context . '/';

        return [
            'asli'    => $urlBase . $newName,
            'resized' => $urlBase . 'resized/' . $resizedName,
            'thumb'   => $urlBase . 'thumbs/'  . $resizedName,
            'width'   => $rw,
            'height'  => $rh,
        ];
    }

    /**
     * Wrapper backward-compatible untuk AuthController@uploadFoto.
     * Mengembalikan array dengan kunci yang sama (asli, resized, thumb, width, height)
     * atau null bila gagal.
     */
    public static function uploadProfilePhoto(array $file): ?array
    {
        $result = self::upload($file, 'profil');
        return $result ?: null;
    }

    /**
     * Pesan error dari panggilan upload() terakhir.
     */
    public static function getLastError(): string
    {
        return self::$lastError;
    }

    /* ------------------------------------------------------------------ */

    /**
     * Resize sumber GD ke maxWidth px lebar, simpan sebagai JPEG kualitas 80.
     *
     * @return array [width, height] dimensi aktual yang disimpan
     */
    private static function resizeAndSave(
        \GdImage $src,
        int $origW,
        int $origH,
        int $maxWidth,
        string $destPath
    ): array {
        if ($origW > $maxWidth) {
            $newW = $maxWidth;
            $newH = (int)round($origH * $maxWidth / $origW);
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $dst = imagecreatetruecolor($newW, $newH);

        /* Latar putih agar PNG transparan tidak hitam saat di-jpeg */
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagejpeg($dst, $destPath, self::QUALITY);
        imagedestroy($dst);

        return [$newW, $newH];
    }
}
