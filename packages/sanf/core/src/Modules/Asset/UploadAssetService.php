<?php

namespace Sanf\Core\Modules\Asset;

use Illuminate\Support\Facades\Storage;
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

        // return result;
        return new AssetUploadResultDto([
            'originName' => $dto->file->getClientOriginalName(),
            'path' => "{$path}{$filename}",
            'fileName' => $filename,
            'url' => $url,
        ]);
    }
}
