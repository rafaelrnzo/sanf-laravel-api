<?php

namespace Sanf\Core\Modules\Contract\Specifications;

use Carbon\Carbon;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Models\ESignDocumentAssigneeEncryptedModel;
use Sanf\Core\Modules\Contract\Models\ESignDocumentAssigneeModel;

class EloquentPaginateDocumentAssigneeByUserIdSpecification
{
    private int $userId;
    private ?int $statusId;
    private ?string $keyword;
    private ?string $sortBy;
    private ?int $skip;
    private ?int $limit;
    private ?int $timestamp;

    /**
     * @param int $userId
     * @param int|null $statusId
     * @param string|null $keyword
     * @param string|null $sortBy
     * @param int|null $skip
     * @param int|null $limit
     * @param int|null $timestamp
     */
    public function __construct(
        int $userId,
        ?int $statusId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    ) {
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->keyword = $keyword;
        $this->sortBy = $sortBy;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->timestamp = $timestamp;
    }

    /**
     * @param ESignDocumentAssigneeModel|ESignDocumentAssigneeEncryptedModel $model
     * @return mixed|\Illuminate\Database\Eloquent\Builder
     */
    public function buildQuery($model)
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

        $sodiumQuery = SodiumEncryption::query();

        $esignDocumentBuilder = function ($query) use ($sodiumQuery) {
            $query->select([
                'document_id',
                'document_name',
                'document_file',
                'expired_at',
                'status_id',
                'reference_no',
                'nonce',
                'category_id',
            ])
                ->where('status_id', '!=', ESignContractStatusEnum::FAILED)
                ->when($this->statusId, function ($query) {
                    $query->where('status_id', '=', $this->statusId);
                })
                ->when($this->keyword, function ($query) use ($sodiumQuery) {
                    $query->where(function ($query) use ($sodiumQuery) {
                        $query->where($sodiumQuery->selectRaw('document_name'), 'ILIKE', '%' . $this->keyword . '%')
                            ->orWhere('document_id', 'ILIKE', '%' . $this->keyword . '%');
                    });
                });
        };

        return $model->newQuery()
            ->select([
                'id',
                'xid',
                'status_id as assignee_status_id',
                'created_at',
                'document_id',
                'nonce',
            ])
            ->with(['eSignDocument' => $esignDocumentBuilder])
            ->whereHas('eSignDocument', $esignDocumentBuilder)
            ->where('user_id', '=', $this->userId)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->timestamp, function ($query) {
                return $query->where('created_at', '>', Carbon::createFromTimestamp($this->timestamp));
            });
    }
}
