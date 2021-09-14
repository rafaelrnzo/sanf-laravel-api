<?php


namespace Sanf\Core\Modules\ContactUs;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Api\Modules\ContactUs\ListAskUsTopicResultDto;

class ListAskUsTopicService implements ApplicationServiceInterface
{

    protected $repository;

    public function __construct(AskUsTopicRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        return new ListAskUsTopicResultDto([
            'list' => $this->repository->list($dto->limit, $dto->offset)
        ]);
    }
}
