<?php

namespace Sanf\Core\Modules\Asset;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;

class UploadAssetService implements ApplicationServiceInterface
{
    private $watermarkTempPath = null;
    private $isWatermarked = false;

    public function execute($dto = null)
    {
        $path = config('image-path.temp');
        $originalFile = $dto->file;

        // process burn image
        if (!empty($dto->burn_text) && in_array($dto->type, [1, 2])) {
            try {
                $dto->file = $this->burnWatermark($dto->file, $dto->burn_text);
                $this->isWatermarked = true;
            } catch (Exception $e) {
                report($e);
            }
        }

        // fall back to the original file if the watermark temp file became unreadable
        // (e.g. removed from the OS temp dir between burnWatermark() and here)
        if (!$dto->file->isReadable()) {
            report(new Exception("Watermarked file unreadable, falling back to original upload: {$dto->file->getPathname()}"));
            $dto->file = $originalFile;
            $this->isWatermarked = false;
        }

        // upload file;
        // note: watermarked files are uploaded by raw content instead of via Storage::putFile(),
        // because SplFileInfo::getRealPath() on the OS temp dir has been observed to return false
        // for these freshly written files on this server even though the file exists and is
        // readable, which crashes putFile()'s internal fopen() call.
        $filename = $this->isWatermarked
            ? $this->uploadRawFile($dto->file, $path)
            : file_upload($dto->file, $path, 'public');

        // if image doesnt exist
        $exist = Storage::disk('minio_post')->exists("{$path}{$filename}");
        throw_if(!$exist, new FileNotFoundException("{$path}"));

        // get url file;
        $url = file_get_temp_url($filename, $path);

        if ($this->watermarkTempPath && file_exists($this->watermarkTempPath)) {
            @unlink($this->watermarkTempPath);
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
     * Upload a file by reading its raw bytes (via getPathname()) instead of
     * Storage::putFile(), which internally relies on SplFileInfo::getRealPath().
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    private function uploadRawFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file, string $path): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $name = Str::random(40) . '.' . $extension;

        Storage::disk('minio_post')->put(
            $path . $name,
            file_get_contents($file->getPathname()),
            'public'
        );

        return $name;
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

        $extension = $file->getClientOriginalExtension() ?: ($file->guessExtension() ?: 'jpg');
        $tempPath = sys_get_temp_dir() . '/' . uniqid('burned_') . '.' . $extension;
        $image->save($tempPath);
        $this->watermarkTempPath = $tempPath;

        return new UploadedFile(
            $tempPath,
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            null,
            true
        );
    }
}
