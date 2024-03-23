<?php

namespace Sanf\Core\Modules\User\Specifications;

use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;

class EloquentPaginateUserActiveSpecification
{
    private ?string $keyword;
    private ?int $skip;
    private ?int $limit;
    private ?string $sortBy;

    /**
     * @param string|null $keyword
     * @param int|null $skip
     * @param int|null $limit
     * @param string|null $sortBy
     */
    public function __construct(?string $keyword, ?int $skip, ?int $limit, ?string $sortBy)
    {
        $this->keyword = $keyword;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(AuthModel $userAuth)
    {
        switch ($this->sortBy) {
            case 'desc':
                $orderBy = 'user_auth.created_at';
                $orderDirection = 'DESC';
                break;
            case 'asc':
            default:
                $orderBy = 'user_auth.created_at';
                $orderDirection = 'ASC';
        }

        $keyword = $this->keyword;

        return $userAuth->newQuery()
            ->select([
                'user_auth.id',
                'user_auth.xid',
                'user_auth.username',
                'user_auth.full_name',
                'user_auth.landline_number',
                'user_auth.phone_number',
                'user_auth.personal_xid',
                'user_auth.company_name',
            ])
            ->whereDoesntHave('deactivateLogs', function ($query) {
                $query->whereIn('user_auth_log.status_id', [
                    UserAuthLogStatusEnum::SUBMIT,
                    UserAuthLogStatusEnum::REJECT,
                ]);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('user_auth.username', 'ilike', strtolower("%{$keyword}%"));
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
