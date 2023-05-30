<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductBuyFilterDto;
use Sanf\Core\Modules\Scanina\Models\ScaninaProductCartModel;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class EloquentGetProductCartByUserSpecification
{
    private object $parameter;

    /**
     * @param object $parameter
     */
    public function __construct(object $parameter)
    {
        $this->parameter = $parameter;
    }

    public function buildQuery(ScaninaProductCartModel $model)
    {
        switch ($this->parameter->sortBy) {
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

        return $model->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->when($this->parameter->keyword, function ($query) {
                return $query->where('snapshot_response_body->name', "ILIKE", '%' . $this->parameter->keyword . '%');
            })->when($this->parameter->skip, function ($query) {
                return $query->skip($this->parameter->skip);
            })->when($this->parameter->limit, function ($query) {
                return $query->limit($this->parameter->limit);
            })->when($this->parameter->timestamp, function ($query) {
                return $query->where('created_at', '>', Carbon::createFromTimestamp($this->parameter->timestamp));
            });
    }
}
