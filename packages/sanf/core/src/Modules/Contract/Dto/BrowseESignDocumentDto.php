<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class BrowseESignDocumentDto extends DataTransferObject
{
    public int $status_id;
    public string $profile_xid;
    public ?string $user_id;
    public ?string $keyword;
    public ?int $skip;
    public ?int $limit;
    public ?string $sort_by;
    public ?int $timestamp;
}
