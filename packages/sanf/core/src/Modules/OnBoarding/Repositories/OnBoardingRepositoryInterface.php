<?php

namespace Sanf\Core\Modules\OnBoarding\Repositories;

interface OnBoardingRepositoryInterface
{
    public function query($specification);

    public function findById($id);

    public function update($id, $request);
}
