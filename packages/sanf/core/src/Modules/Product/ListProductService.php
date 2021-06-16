<?php


namespace Sanf\Core\Modules\Product;


use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Sanf\Api\Modules\Common\UploadFileResultDto;
use Sanf\Api\Modules\Product\ListProductResultDto;
use Sanf\Core\Modules\ServiceInterface;

class ListProductService implements ServiceInterface
{

    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function run($dto)
    {
        // prepare filter;
        $filter = [];
        if ($dto->id)
            $filter += ['id' => "id = {$dto->id}"];

        if ($dto->title)
            $filter += ['title' => "title like '%{$dto->title}%'"];

        // concat with 'and' separator;
        $search  = implode(' and ', $filter);

        // sent list data;
        return new ListProductResultDto([
            'list' => $this->repository->list($dto->limit, $dto->offset, $search)
        ]);
    }
}