<?php


namespace Sanf\Core\Modules\ContactUs;


class EloquentAskUs implements AskUsRepositoryInterface
{

    /** @var AskUsModel $model */
    protected $model;

    public function __construct(AskUsModel $model)
    {
        $this->model = $model;
    }

    public function save($data)
    {
        return $this->model
            ->newQuery()
            ->create($data);
    }
}