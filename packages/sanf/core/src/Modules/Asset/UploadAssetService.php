<?php

namespace Sanf\Core\Modules\Asset;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
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
                $dto->file = $this->burnWatermark($dto->file, $dto->burn_text);
            } catch (Exception $e) {
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

    /**
     * Burn SANF logo, coordinates, address, and datetime into the image.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $burnText
     * @return \Illuminate\Http\UploadedFile
     */
    private function burnWatermark($file, array $burnText)
    {
        $image = ImageManagerStatic::make($file->getRealPath());
        $width = $image->width();
        $height = $image->height();

        $fontSize = max(24, intval($width * 0.028));
        $logoHeight = intval($fontSize * 2.2);
        $lineHeight = intval($fontSize * 1.35);
        $padding = intval($width * 0.035);
        $wrapLength = 55;

        $wrappedTexts = [];
        foreach ($burnText as $text) {
            $wrapped = explode("\n", wordwrap($text, $wrapLength, "\n"));
            foreach ($wrapped as $wLine) {
                $wLine = trim($wLine);
                if ($wLine !== '') {
                    $wrappedTexts[] = $wLine;
                }
            }
        }

        $logoPath = public_path('assets/png/sanf-logo-blue.png');
        $logoWidth = 0;
        $logo = null;
        if (file_exists($logoPath)) {
            $logo = ImageManagerStatic::make($logoPath);
            $logo->resize(null, $logoHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
            $logoWidth = $logo->width();
        }

        $textHeight = count($wrappedTexts) * $lineHeight;
        $gap = $logo ? intval($fontSize * 0.8) : 0;
        $logoAllocatedHeight = $logo ? $logoHeight : 0;

        $boxHeight = $logoAllocatedHeight + $gap + $textHeight;
        $y1 = $height - $boxHeight - $padding;

        if ($logo) {
            $image->insert($logo, 'top-left', $width - $padding - $logoWidth, $y1);
        }

        $fontPath = public_path('assets/fonts/Questrial-Regular.ttf');
        if (!file_exists($fontPath)) {
            $fontPath = base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf');
        }

        $offsetVal = max(1, intval($fontSize / 20));
        $yOffset = $y1 + $logoAllocatedHeight + $gap;
        foreach ($wrappedTexts as $text) {
            $offsets = [
                [-$offsetVal, -$offsetVal],
                [-$offsetVal, $offsetVal],
                [$offsetVal, -$offsetVal],
                [$offsetVal, $offsetVal]
            ];
            foreach ($offsets as $offset) {
                $image->text($text, $width - $padding + $offset[0], $yOffset + $offset[1], function ($font) use ($fontPath, $fontSize) {
                    if (file_exists($fontPath)) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                    } else {
                        $font->size(7);
                    }
                    $font->color('#000000');
                    $font->align('right');
                    $font->valign('top');
                });
            }

            $image->text($text, $width - $padding, $yOffset, function ($font) use ($fontPath, $fontSize) {
                if (file_exists($fontPath)) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                } else {
                    $font->size(7);
                }
                $font->color('#ffffff');
                $font->align('right');
                $font->valign('top');
            });
            $yOffset += $lineHeight;
        }

        $tempPath = sys_get_temp_dir() . '/' . uniqid('burned_') . '.' . $file->getClientOriginalExtension();
        $image->save($tempPath);

        return new UploadedFile(
            $tempPath,
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            null,
            true
        );
    }
}
