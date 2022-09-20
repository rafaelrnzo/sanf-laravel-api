<?php

namespace Sanf\Core\Modules\User\Specifications;

use Sanf\Core\Modules\User\UserAuthLogModel;

class EloquentBrowseUserAuthLogByUserIdSpecification
{
    private int $userId;
    private ?array $statusId;
    private ?string $keyword;
    private ?int $limit;
    private ?int $skip;
    private ?string $sortBy;

    public function __construct(
        int $userId,
        ?array $statusId,
        ?string $keyword,
        ?int $limit,
        ?int $skip,
        ?string $sortBy
    ) {
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->keyword = $keyword;
        $this->limit = $limit;
        $this->skip = $skip;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(UserAuthLogModel $model)
    {
        switch ($this->sortBy) {
            case 'desc':
                $orderBy = 'user_auth_log.created_at';
                $orderDirection = 'DESC';
                break;
            case 'asc':
            default:
                $orderBy = 'user_auth_log.created_at';
                $orderDirection = 'ASC';
        }

        $statusId = $this->statusId;
        $keyword = $this->keyword;
        return $model->newQuery()
            ->select([
                'user_auth_log.id',
                'user_auth_log.xid',
                'user_auth_log.user_id',
                'user_auth_log.restore_expired_at',
                'user_auth_log.created_at',

                'user_auth.username',
                'user_auth.full_name',
                'user_auth.landline_number',
                'user_auth.phone_number',
                'user_auth.personal_xid',
                'user_auth.company_name',
            ])
            ->join('user_auth', 'user_auth.id', '=', 'user_auth_log.user_id')
            ->where('user_auth_log.user_id', '=', $this->userId)
            ->when($statusId, function ($query) {
                $query->whereIn('user_auth_log.status_id', $this->statusId);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('user_auth.username', 'ilike', strtolower("%{$keyword}%"))
                    ->orWhere('user_auth.full_name', 'ilike', strtolower("%{$keyword}%"))
                    ->orWhere('user_auth.landline_number', 'ilike', strtolower("%{$keyword}%"))
                    ->orWhere('user_auth.phone_number', 'ilike', strtolower("%{$keyword}%"))
                    ->orWhere('user_auth.personal_xid', 'ilike', strtolower("%{$keyword}%"))
                    ->orWhere('user_auth.company_name', 'ilike', strtolower("%{$keyword}%"));
            })
            ->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })
            ->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })
            ->orderBy($orderBy, $orderDirection);
    }
}
