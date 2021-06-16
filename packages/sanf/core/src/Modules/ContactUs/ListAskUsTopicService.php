<?php


namespace Sanf\Core\Modules\ContactUs;


use Sanf\Api\Modules\ContactUs\ListAskUsTopicResultDto;
use Sanf\Core\Modules\ServiceInterface;

class ListAskUsTopicService implements ServiceInterface
{

    protected $repository;

    public function __construct(AskUsTopicRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function run($dto)
    {
        return new ListAskUsTopicResultDto([
            'list' => $this->repository->list($dto->limit, $dto->offset)
        ]);
    }
}