<?php


namespace Sanf\Core\Modules\ContactUs;


class EloquentAskUsTopicRepository implements AskUsTopicRepositoryInterface
{

    /** @var AskUsTopicModel $model */
    protected $model;

    public function __construct(AskUsTopicModel $model)
    {
        $this->model = $model;
    }

    public function list($limit, $offset)
    {
        return $this->model
            ->newQuery()
            ->select([
                'id',
                'name'
            ])
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
}