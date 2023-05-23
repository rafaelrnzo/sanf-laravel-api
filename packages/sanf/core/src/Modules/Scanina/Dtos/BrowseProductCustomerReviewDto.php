<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductCustomerReviewDto extends FlexibleDataTransferObject
{
    public ?int $id;
    public ?object $userSnapshot;
    public ?array $images;
    public ?array $statisfiedWith;
    public ?array $unsatisfiedWith;
    public ?int $rating;
    public ?bool $isCommentHidden;
    public ?bool $isAnonymous;
    public ?int $createdAt;
    public ?int $updatedAt;
}
