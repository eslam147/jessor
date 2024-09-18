<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait TenantImageTrait
{
    protected function uploadImage(UploadedFile $newImage, string $path, $oldImage = null, bool $resizeImage = true, ?string $imageName = null)
    {
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $path)) {
            throw new \InvalidArgumentException('The path can only contain letters, numbers, and underscores.');
        }
        if (! empty($newImage) && ! is_null($newImage)) {
            $this->removeImage($oldImage);
            $imageName = (! empty($imageName) ? "{$imageName}.{$newImage->guessExtension()}" : time() . "_{$newImage->hashName()}");
            if ($resizeImage) {
                resizeImage($newImage);
            }
            $destinationPath = storage_path("app/public/{$path}");
            $newImage->move($destinationPath, $imageName);
            return $path . DIRECTORY_SEPARATOR . $imageName;
        }
        return $oldImage;
    }


    protected function removeImage($path, $disk = 'local')
    {
        if (! empty($path) && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}