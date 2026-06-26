<?php
/**
 * UploadHelper (Stub)
 * 
 * CATATAN: Ini adalah stub (implementasi palsu sementara) 
 * yang akan diisi oleh pengerjaan Orang 2 (fitur produk/upload).
 */
class UploadHelper
{
    public static function uploadProfilePhoto(array $file): ?array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid() . time() . '.' . $ext;
        $dest = PUBLIC_PATH . '/assets/uploads/profil/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return [
                'asli' => '/assets/uploads/profil/' . $newName,
                'resized' => '/assets/uploads/profil/' . $newName,
                'thumb' => '/assets/uploads/profil/' . $newName,
            ];
        }

        return null;
    }
}
