<?php

namespace Sanf\Core\Modules\ContactUs;

use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Services\ApplicationServiceInterface;

class AskUsSubmitService implements ApplicationServiceInterface
{
    protected $repository;

    public function __construct(AskUsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $images = [];

        // move asset;
        if ($dto->images) {

            //TODO REFACTOR
            foreach ($dto->images as $image) {
                $newPath = config('image-path.ask-us');
                $tempPath = config('image-path.temp');

                $exist = Storage::disk('minio_post')->exists("{$newPath}{$image}");
                if (!$exist) {
                    Storage::disk('minio_post')->move("{$tempPath}{$image}", "{$newPath}{$image}");
                }

                $images[] = [
                    'file_name' => $image,
                    'directory' => $newPath,
                    'path' => "{$newPath}{$image}",
                    'mime_type' => Storage::disk('minio_post')->getMimeType("{$newPath}{$image}"),
                ];
            }
        }

        // prepare data;
        $data = $dto->toArray();
        $data['images'] = json_encode($images);

        // store data;
        //TODO USE REPOSITORY
        $data['topic'] = optional(AskUsTopicModel::find($data['topic_id']))->name;
        $this->repository->save($data);

        return $data;
    }
}
