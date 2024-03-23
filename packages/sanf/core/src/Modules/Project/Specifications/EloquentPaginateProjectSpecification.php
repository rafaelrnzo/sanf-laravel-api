<?php

namespace Sanf\Core\Modules\Project\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\Project\Models\ProjectModel;
use Sanf\Core\Modules\Project\ProjectStatus;

class EloquentPaginateProjectSpecification
{
    private ?int $skip;
    private ?int $limit;
    private ?string $sortBy;
    private ?int $timestamp;
    private ?string $keyword;

    public function __construct(?int $skip, ?int $limit, ?string $sortBy, ?int $timestamp, ?string $keyword)
    {
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sortBy = $sortBy;
        $this->timestamp = $timestamp;
        $this->keyword = $keyword;
    }

    public function buildQuery(ProjectModel $model)
    {
        switch ($this->sortBy) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'created_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            case 'newest':
            default:
                $orderBy = 'created_at';
                $orderDirection = 'DESC';
        }

        $query = $model->newQuery()
            ->with('user')
            ->orderBy($orderBy, $orderDirection)
            ->where('status_id', ProjectStatus::PUBLISHED)
            ->when($this->keyword, function ($query) {
                return $query->where('title', 'ILIKE', '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->timestamp, function ($query) {
                return $query->where('published_at', '>', Carbon::createFromTimestamp($this->timestamp));
            });

        return $query;
    }
}
