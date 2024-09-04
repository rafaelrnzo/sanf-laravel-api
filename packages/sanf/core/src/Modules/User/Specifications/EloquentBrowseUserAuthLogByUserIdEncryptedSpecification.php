<?php

namespace Sanf\Core\Modules\User\Specifications;

use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Modules\User\UserAuthLogEncryptedModel;

class EloquentBrowseUserAuthLogByUserIdEncryptedSpecification
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

    public function buildQuery(UserAuthLogEncryptedModel $model)
    {
        switch ($this->sortBy) {
            case 'desc':
                $orderBy = 'created_at';
                $orderDirection = 'DESC';
                break;
            case 'asc':
            default:
                $orderBy = 'created_at';
                $orderDirection = 'ASC';
        }

        $statusId = $this->statusId;
        $keyword = $this->keyword;

        $userAuthIds = null;
        if ($keyword) {
            $userAuthIds = AuthEncryptedModel::query()
                ->select('id')
                ->where('username', 'ilike', strtolower("%{$keyword}%"))
                ->orWhere('full_name', 'ilike', strtolower("%{$keyword}%"))
                ->orWhere('landline_number', 'ilike', strtolower("%{$keyword}%"))
                ->orWhere('phone_number', 'ilike', strtolower("%{$keyword}%"))
                ->orWhere('personal_xid', 'ilike', strtolower("%{$keyword}%"))
                ->orWhere('company_name', 'ilike', strtolower("%{$keyword}%"))
                ->pluck('id')
                ->toArray();
        }

        return $model->newQuery()
            ->select([
                'id',
                'xid',
                'user_id',
                'restore_expired_at',
                'created_at',
                'nonce',
            ])
            ->with([
                'user' => function ($query) {
                    $query->select([
                        'id',
                        'username',
                        'full_name',
                        'landline_number',
                        'phone_number',
                        'personal_xid',
                        'company_name',
                        'nonce',
                    ]);
                },
            ])
            ->where('user_id', '=', $this->userId)
            ->when($statusId, function ($query) {
                $query->whereIn('status_id', $this->statusId);
            })
            ->when(!is_null($userAuthIds), function ($query) use ($userAuthIds) {
                $query->whereIn('user_id', $userAuthIds);
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
