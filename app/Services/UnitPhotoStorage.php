<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UnitPhotoStorage
{
    public function store(UploadedFile $upload): string
    {
        $size = @getimagesize($upload->getPathname());
        if (! $size || $size[0] * $size[1] > 24000000) {
            throw ValidationException::withMessages(['photos' => 'Resolusi foto maksimal 24 megapiksel.']);
        }
        $image = @imagecreatefromstring($upload->getContent());
        if (! $image) {
            throw ValidationException::withMessages(['photos' => 'Foto tidak dapat diproses.']);
        }
        $bufferLevel = ob_get_level();
        try {
            if ($upload->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
                $exif = @exif_read_data($upload->getPathname());
                $orientation = (int) ($exif['Orientation'] ?? 1);
                if (in_array($orientation, [2, 4, 5, 7], true)) {
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                }
                $angle = match ($orientation) {
                    3, 4 => 180,
                    5, 6 => -90,
                    7, 8 => 90,
                    default => 0,
                };
                if ($angle !== 0) {
                    $rotated = imagerotate($image, $angle, 0);
                    if (! $rotated instanceof GdImage) {
                        throw new \RuntimeException('Rotasi foto gagal.');
                    }
                    imagedestroy($image);
                    $image = $rotated;
                }
            }
            $width = imagesx($image);
            $height = imagesy($image);
            $scale = min(1, 1920 / max($width, $height));
            if ($scale < 1) {
                $resized = imagecreatetruecolor(max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale)));
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, imagesx($resized), imagesy($resized), $width, $height);
                imagedestroy($image);
                $image = $resized;
            }
            imagesavealpha($image, true);
            ob_start();
            $encoded = imagewebp($image, null, 82);
            $contents = ob_get_clean();
            if (! $encoded || ! $contents) {
                throw new \RuntimeException('Kompresi foto gagal.');
            }
            $path = 'unit-photos/'.Str::uuid().'.webp';
            if (! Storage::disk('local')->put($path, $contents)) {
                throw ValidationException::withMessages(['photos' => 'Foto gagal disimpan.']);
            }

            return $path;
        } finally {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }
            imagedestroy($image);
        }
    }
}
