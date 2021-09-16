<?php


namespace Sanf\Core\Modules\Asset;


use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;

class UploadAssetService implements ApplicationServiceInterface
{

    public function execute($dto = null)
    {
        $configs = [
            1 => 'image-path',
        ];
        $path = config("{$configs[$dto->type]}.temp");

        // upload file;
        $filename = file_upload($dto->file, $path, 'public');

        // if image doesnt exist
        $exist = Storage::exists("{$path}{$filename}");
        throw_if(!$exist, new FileNotFoundException("{$path}"));

        // get url file;
        $url = file_get_url($filename, $path);

        // return result;
        return new AssetUploadResultDto([
            'origin_name' => $dto->file->getClientOriginalName(),
            'path' => "/{$path}/{$filename}",
            'file_name' => $filename,
            'url' => $url
        ]);

    }
}
