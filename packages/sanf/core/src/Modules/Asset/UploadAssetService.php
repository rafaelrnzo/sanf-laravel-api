<?php

namespace Sanf\Core\Modules\Asset;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;

class UploadAssetService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $path = config('image-path.temp');

        // process burn image
        if (!empty($dto->burn_text) && in_array($dto->type, [1, 2])) {
            try {
                $image = \Intervention\Image\ImageManagerStatic::make($dto->file->getRealPath());
                $width = $image->width();
                $height = $image->height();

                $padding = 20;
                $lineHeight = 30;
                $lines = count($dto->burn_text);
                $rectHeight = ($padding * 2) + ($lineHeight * $lines);
                
                $image->rectangle(0, $height - $rectHeight, $width, $height, function ($draw) {
                    $draw->background('rgba(0, 0, 0, 0.5)');
                });

                $yOffset = $height - $rectHeight + $padding + 20; // 20 is approx font baseline adjustment
                $fontPath = public_path('assets/fonts/Questrial-Regular.ttf');
                if (!file_exists($fontPath)) {
                    $fontPath = base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf');
                }

                foreach ($dto->burn_text as $text) {
                    $image->text($text, 20, $yOffset, function($font) use ($fontPath) {
                        if (file_exists($fontPath)) {
                            $font->file($fontPath);
                            $font->size(24);
                        } else {
                            $font->size(5); // fallback to built-in if no font found
                        }
                        $font->color('#ffffff');
                    });
                    $yOffset += $lineHeight;
                }

                $tempPath = sys_get_temp_dir() . '/' . uniqid('burned_') . '.' . $dto->file->getClientOriginalExtension();
                $image->save($tempPath);
                
                // Replace the DTO file with the newly saved file
                $dto->file = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    $dto->file->getClientOriginalName(),
                    $dto->file->getClientMimeType(),
                    null,
                    true // test=true to bypass is_uploaded_file check
                );
            } catch (\Exception $e) {
                // If burning fails, report and fallback to original file
                report($e);
            }
        }

        // upload file;
        $filename = file_upload($dto->file, $path, 'public');

        // if image doesnt exist
        $exist = Storage::disk('minio_post')->exists("{$path}{$filename}");
        throw_if(!$exist, new FileNotFoundException("{$path}"));

        // get url file;
        $url = file_get_temp_url($filename, $path);

        if (isset($tempPath) && file_exists($tempPath)) {
            @unlink($tempPath);
        }

        // return result;
        return new AssetUploadResultDto([
            'originName' => $dto->file->getClientOriginalName(),
            'path' => "{$path}{$filename}",
            'fileName' => $filename,
            'url' => $url,
        ]);
    }
}
