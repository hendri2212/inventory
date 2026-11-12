<?php

use App\Services\UnitPhotoStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

test('unit photos are resized and compressed to webp while retaining portrait or landscape proportions', function (int $width, int $height, int $expectedWidth, int $expectedHeight) {
    Storage::fake('local');
    $upload = UploadedFile::fake()->image('photo.jpg', $width, $height);
    $path = app(UnitPhotoStorage::class)->store($upload);
    $stored = Storage::disk('local')->get($path);
    $size = getimagesizefromstring($stored);
    expect($size[0])->toBe($expectedWidth)->and($size[1])->toBe($expectedHeight)
        ->and($size['mime'])->toBe('image/webp')
        ->and(strlen($stored))->toBeLessThan(filesize($upload->getPathname()));
})->with([[3000, 2000, 1920, 1280], [2000, 3000, 1280, 1920]]);

test('small images are not enlarged and png transparency is preserved', function () {
    Storage::fake('local');
    $image = imagecreatetruecolor(20, 30);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
    ob_start();
    imagepng($image);
    $png = ob_get_clean();
    imagedestroy($image);
    $path = app(UnitPhotoStorage::class)->store(UploadedFile::fake()->createWithContent('transparent.png', $png));
    $result = imagecreatefromstring(Storage::disk('local')->get($path));
    expect(imagesx($result))->toBe(20)->and(imagesy($result))->toBe(30)
        ->and(imagecolorsforindex($result, imagecolorat($result, 0, 0))['alpha'])->toBe(127);
    imagedestroy($result);
});

test('invalid images are rejected before storage', function () {
    Storage::fake('local');
    expect(fn () => app(UnitPhotoStorage::class)->store(UploadedFile::fake()->createWithContent('broken.jpg', 'invalid')))
        ->toThrow(ValidationException::class);
    expect(Storage::disk('local')->allFiles())->toBe([]);
});
