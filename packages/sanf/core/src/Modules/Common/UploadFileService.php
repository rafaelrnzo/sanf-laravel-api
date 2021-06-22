<?php


namespace Sanf\Core\Modules\Common;


use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Api\Modules\Common\UploadFileResultDto;

class UploadFileService implements ApplicationServiceInterface
{

    public function execute($dto)
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
        return new UploadFileResultDto([
            'origin_name' => $dto->file->getClientOriginalName(),
            'file_name' => $filename,
            'url' => $url
        ]);

    }
}
