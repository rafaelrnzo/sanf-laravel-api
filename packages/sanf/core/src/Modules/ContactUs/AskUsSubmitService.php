<?php


namespace Sanf\Core\Modules\ContactUs;


use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\ServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AskUsSubmitService implements ApplicationServiceInterface
{

    protected $repository;

    public function __construct(AskUsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto)
    {
        $images = [];

        // move asset;
        foreach ($dto->images as $image) {
            $newPath = config('image-path.ask-us');
            $tempPath = config('image-path.temp');

            $exist = Storage::exists("{$newPath}{$image}");
            if(!$exist){
                Storage::move("{$tempPath}{$image}", "{$newPath}{$image}");
            }

            $images[] = [
                'file_name' => $image,
                'directory' => $newPath,
                'path' => "{$newPath}{$image}",
                'mime_type' => Storage::getMimeType("{$newPath}{$image}")
            ];
        }

        // prepare data;
        $data = $dto->toArray();
        $data['images'] = json_encode($images);

        // store data;
        //TODO USE REPOSITORY
        $data['topic'] = optional(AskUsTopicModel::find($data['topic_id']))->name;
        $this->repository->save($data);

        return true;
    }
}
