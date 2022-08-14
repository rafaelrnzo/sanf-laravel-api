<?php

namespace Sanf\Core\Modules\Faq\Repositories;

interface FaqCategoryRepositoryInterface
{
    public function list($limit, $offset, $orderBy, $orderDirection, $search, $searchFaq);

    public function findById($id);
}
