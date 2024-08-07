<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait TenantImageTrait
{
    protected function uploadImage(UploadedFile $newImage, string $path, $oldImage = null, bool $resizeImage = true, ?string $imageName = null)
    {
        if (! empty($newImage) && ! is_null($newImage)) {
            $this->removeImage($oldImage);

            $imageName = (! empty($imageName) ? "{$imageName}.{$newImage->guessExtension()}" : time() . "_{$newImage->hashName()}");

            if ($resizeImage) {
                resizeImage($newImage);
            }

            $destinationPath = storage_path("app/public/{$path}");
            $newImage->move($destinationPath, $imageName);

            return "{$path}/{$imageName}";
        }
        return $oldImage;
    }


    protected function removeImage($path)
    {
        if (! empty($path) && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}