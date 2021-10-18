<?php


namespace Sanf\Core\Modules\ContactUs;


use NbsPhp\Core\Repositories\AbstractEloquentRepository;

class EloquentAskUsTopicRepository extends AbstractEloquentRepository implements AskUsTopicRepositoryInterface
{

    /** @var AskUsTopicModel $model */
    protected $model;

    public function __construct(AskUsTopicModel $model)
    {
        $this->model = $model;
    }

    public function list($limit, $offset)
    {
        $result = $this->model
            ->newQuery()
            ->select([
                'id',
                'name'
            ])
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $this->stripEloquentModel($result);
    }
}
