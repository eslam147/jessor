<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TenantMediaService
{
    protected static function checkPath($path)
    {
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $path)) {
            throw new \InvalidArgumentException('The path can only contain letters, numbers, and underscores.');
        }
        return $path;
    }

    public static function uploadImage(UploadedFile $newImage, string $path, $oldImage = null, bool $resizeImage = true, ?string $imageName = null)
    {
        self::checkPath($path);

        if (! empty($newImage) && ! is_null($newImage)) {
            self::removeImage($oldImage);

            $imageName = (! empty($imageName) ? "{$imageName}.{$newImage->guessExtension()}" : time() . "_{$newImage->hashName()}");

            if ($resizeImage) {
                // resizeImage($newImage);
            }

            $destinationPath = storage_path("app/public/{$path}");
            $newImage->move($destinationPath, $imageName);

            return "{$path}" . DIRECTORY_SEPARATOR . "{$imageName}";
        }
        return $oldImage;
    }

    public static function removeImage($path, $disk = 'local')
    {
        if (! empty($path) && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}