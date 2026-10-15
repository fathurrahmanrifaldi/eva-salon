<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

/**
 * Upload foto: validasi dilakukan di FormRequest (MIME + 2 MB). Di sini: ubah ukuran
 * (lebar maks 1200 px), konversi ke WebP, simpan dengan nama acak.
 * Disimpan langsung di public/uploads agar tidak butuh `storage:link` (hosting tanpa SSH).
 */
class ImageService
{
    public function store(UploadedFile $file, string $dir): string
    {
        // shortcut: orientasi EXIF tidak dikoreksi, tambahkan jika foto dari ponsel tampak miring.
        $img = @imagecreatefromstring($file->get());
        if (! $img) {
            throw new RuntimeException('Berkas gambar tidak bisa dibaca.');
        }

        if (imagesx($img) > 1200) {
            $img = imagescale($img, 1200);
        }

        $path = "uploads/$dir/".bin2hex(random_bytes(12)).'.webp';
        $full = public_path($path);
        if (! is_dir(dirname($full))) {
            mkdir(dirname($full), 0755, true);
        }

        imagewebp($img, $full, 82);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/') && is_file(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
