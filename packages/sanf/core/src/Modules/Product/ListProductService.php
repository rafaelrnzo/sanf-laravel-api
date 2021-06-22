<?php


namespace Sanf\Core\Modules\Product;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Api\Modules\Product\ListProductResultDto;

class ListProductService implements ApplicationServiceInterface
{

    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto)
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
