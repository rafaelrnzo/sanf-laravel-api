<?php

namespace Sanf\Core\Modules\News;

class EloquentNewsRepository implements NewsRepositoryInterface
{

    /** @var NewsModel $model */
    protected $model;

    public function __construct(NewsModel $model)
    {
        $this->model = $model;
    }

    public function list($dto)
    {
        switch ($dto->sort_by) {
            case 'title':
                $orderBy = 'news.title';
                $orderDir = 'ASC';
                break;
            case 'title_desc':
                $orderBy = 'news.title';
                $orderDir = 'DESC';
                break;
            case 'oldest':
                $orderBy = 'news.created_at';
                $orderDir = 'ASC';
                break;
            case 'latest':
            default:
                $orderBy = 'news.created_at';
                $orderDir = 'DESC';
        }
        $query = $this->model->newQuery()
            ->when($dto->keyword, function ($query) use ($dto) {
                return $query->where('news.title', 'ilike', "%{$dto->keyword}%");
            });

        $total = $query->count();

        $lists = $query->select([
            'news.xid',
            'news.title',
            'news.image_url',
            'news.link_url',
            'news.created_at',
        ])
            ->orderBy($orderBy, $orderDir)
            ->skip($dto->skip)
            ->limit($dto->limit)
            ->get();

        return [
            'total' => $total,
            'count' => $lists->count(),
            'lists' => $lists,
        ];
    }
}