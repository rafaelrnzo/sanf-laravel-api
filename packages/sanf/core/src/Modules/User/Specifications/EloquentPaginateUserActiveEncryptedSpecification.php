<?php

namespace Sanf\Core\Modules\User\Specifications;

use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\UserAuthLogModel;

class EloquentPaginateUserActiveEncryptedSpecification
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

    public function buildQuery(AuthEncryptedModel $userAuth)
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

        $keyword = $this->keyword;

        $sodiumQuery = SodiumEncryption::query();

        $excludeIds = UserAuthLogModel::query()
            ->whereIn('status_id', [
                UserAuthLogStatusEnum::SUBMIT,
                UserAuthLogStatusEnum::REJECT,
            ])
            ->pluck('user_id')
            ->toArray();

        return $userAuth->newQuery()
            ->select([
                'id',
                'xid',
                'username',
                'full_name',
                'landline_number',
                'phone_number',
                'personal_xid',
                'company_name',
                'nonce',
            ])
            ->whereNotIn('id', $excludeIds)
            ->when($keyword, function ($query) use ($keyword, $sodiumQuery) {
                $query->where($sodiumQuery->selectRaw('username'), 'ilike', strtolower("%{$keyword}%"));
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
