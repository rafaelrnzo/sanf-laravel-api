<?php


namespace Sanf\Core\Modules\Common;


use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Sanf\Api\Modules\Common\UploadFileResultDto;
use Sanf\Core\Modules\ServiceInterface;

class UploadImageService implements ServiceInterface
{

    public function run($dto)
    {
        $array = [
            1 => 'temp',
        ];
        $path = config("image-path.{$array[$dto->type]}");

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