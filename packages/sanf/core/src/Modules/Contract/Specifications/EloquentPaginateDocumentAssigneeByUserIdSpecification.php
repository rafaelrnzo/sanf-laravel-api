<?php

namespace Sanf\Core\Modules\Contract\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
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

    public function buildQuery(ESignDocumentAssigneeModel $model)
    {
        switch ($this->sortBy) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'esign_document_assignee.created_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            case 'newest':
            default:
                $orderBy = 'esign_document_assignee.created_at';
                $orderDirection = 'DESC';
        }

        return $model->newQuery()
            ->select([
                'esign_document_assignee.id',
                'esign_document_assignee.xid',
                'esign_document_assignee.status_id as assignee_status_id',
                'esign_document_assignee.created_at',

                'esign_document.document_id',
                'esign_document.document_name',
                'esign_document.document_file',
                'esign_document.expired_at',
                'esign_document.status_id',
            ])
            ->join('esign_document', 'esign_document.document_id', '=', 'esign_document_assignee.document_id')
            ->where('esign_document.status_id', '!=', ESignContractStatusEnum::FAILED)
            ->where('esign_document_assignee.user_id', '=', $this->userId)
            ->orderBy($orderBy, $orderDirection)
            ->when($this->keyword, function ($query) {
                return $query->where('esign_document.document_name', "ILIKE", '%' . $this->keyword . '%')
                    ->orWhere('esign_document_assignee.document_id', "ILIKE", '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->timestamp, function ($query) {
                return $query->where('esign_document_assignee.created_at', '>', Carbon::createFromTimestamp($this->timestamp));
            });
    }
}