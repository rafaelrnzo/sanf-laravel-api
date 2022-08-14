<?php

namespace Sanf\Core\Modules\Setting\Repositories;

interface FaqCategoryRepositoryInterface
{
    public function query($specification);

    public function findById($id);

    public function create($request);

    public function update($id, $request);

    public function destroy($id);
}
