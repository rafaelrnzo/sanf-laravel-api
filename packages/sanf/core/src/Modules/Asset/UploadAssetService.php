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

        // upload file;
        $filename = file_upload($dto->file, $path, 'public');

        // if image doesnt exist
        $exist = Storage::disk('minio_post')->exists("{$path}{$filename}");
        throw_if(!$exist, new FileNotFoundException("{$path}"));

        // get url file;
        $url = file_get_temp_url($filename, $path);

        // return result;
        return new AssetUploadResultDto([
            'originName' => $dto->file->getClientOriginalName(),
            'path' => "{$path}{$filename}",
            'fileName' => $filename,
            'url' => $url,
        ]);
    }
}
