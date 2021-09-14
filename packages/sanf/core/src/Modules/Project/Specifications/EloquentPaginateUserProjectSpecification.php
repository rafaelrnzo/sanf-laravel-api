<?php


namespace Sanf\Core\Modules\Project\Specifications;


use Sanf\Core\Modules\Project\Models\ProjectModel;

class EloquentPaginateUserProjectSpecification
{
    private int $userId;
    private ?int $skip;
    private ?int $limit;
    private ?string $sortBy;
    private ?string $keyword;

    public function __construct(int $userId, ?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        $this->userId = $userId;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sortBy = $sortBy;
        $this->keyword = $keyword;
    }

    public function buildQuery(ProjectModel $model)
    {
        switch ($this->sortBy) {
            case 'oldest':
                $orderBy = 'project.created_at';
                $orderDirection = 'ASC';
                break;
            default:
                $orderBy = 'project.created_at';
                $orderDirection = 'DESC';
        }

        $query = $model->newQuery()
            ->with('status')
            ->where('user_id', $this->userId)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->keyword, function ($query) {
                return $query->where('title', "ILIKE", '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            });

        return $query;
    }
}
