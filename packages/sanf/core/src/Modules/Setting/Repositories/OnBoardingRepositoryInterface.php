<?php

namespace Sanf\Core\Modules\Setting\Repositories;

interface OnBoardingRepositoryInterface
{
    public function query($specification);

    public function findById($id);

    public function update($id, $request);
}
