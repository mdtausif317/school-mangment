<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentPhotoService
{
  public const WIDTH = 200;

  public const HEIGHT = 250;

  public function store(UploadedFile $file, int $schoolId): string
  {
    $directory = "students/{$schoolId}";
    Storage::disk('public')->makeDirectory($directory);

    $filename = $directory.'/'.Str::uuid().'.jpg';
    $fullPath = Storage::disk('public')->path($filename);

    $this->resizeToStandard($file->getRealPath(), $fullPath);

    return $filename;
  }

  public function delete(?string $path): void
  {
    if ($path) {
      Storage::disk('public')->delete($path);
    }
  }

  protected function resizeToStandard(string $sourcePath, string $destPath): void
  {
    if (! extension_loaded('gd')) {
      copy($sourcePath, $destPath);

      return;
    }

    [$srcW, $srcH, $type] = getimagesize($sourcePath);
    $src = match ($type) {
      IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
      IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
      IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
      default => throw new \InvalidArgumentException('Unsupported image type.'),
    };

    $targetW = self::WIDTH;
    $targetH = self::HEIGHT;
    $scale = max($targetW / $srcW, $targetH / $srcH);
    $resizeW = (int) ceil($srcW * $scale);
    $resizeH = (int) ceil($srcH * $scale);

    $resized = imagecreatetruecolor($resizeW, $resizeH);
    imagecopyresampled($resized, $src, 0, 0, 0, 0, $resizeW, $resizeH, $srcW, $srcH);

    $cropX = (int) floor(($resizeW - $targetW) / 2);
    $cropY = (int) floor(($resizeH - $targetH) / 2);

    $final = imagecreatetruecolor($targetW, $targetH);
    imagecopy($final, $resized, 0, 0, $cropX, $cropY, $targetW, $targetH);

    imagejpeg($final, $destPath, 90);

    imagedestroy($src);
    imagedestroy($resized);
    imagedestroy($final);
  }
}
