<?php

namespace Sanf\Core\Modules\Promo;

interface PromoRepositoryInterface
{
    public function list($dto);

    public function findByXid(string $xid);
}
