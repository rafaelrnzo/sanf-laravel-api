<?php


namespace Sanf\Core\Modules\ContactUs;


use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Sanf\Core\Modules\ServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AskUsSubmitService implements ServiceInterface
{

    protected $repository;

    public function __construct(AskUsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function run($dto)
    {
        $images = [];

        // move asset;
        foreach ($dto->images as $image) {
            $tempPath = config('image-path.temp');

            // prevent image doesn't exist;
            $exist = Storage::exists("{$tempPath}{$image}");
            throw_if(!$exist, new FileNotFoundException("{$tempPath}{$image}"));

            $newPath = config('image-path.ask-us');
            Storage::copy("{$tempPath}{$image}", "{$newPath}{$image}");

            $images[] = [
                'file_name' => $image,
                'directory' => $newPath,
                'path' => "{$newPath}{$image}",
                'mime_type' => Storage::getMimeType("{$tempPath}{$image}")
            ];
        }

        // prepare data;
        $data = $dto->toArray();
        $data['images'] = json_encode($images);
        $data['modified_by'] = json_encode([]); // TODO filled this;

        // store data;
        $query = $this->repository->save($data);

        return true;
    }
}