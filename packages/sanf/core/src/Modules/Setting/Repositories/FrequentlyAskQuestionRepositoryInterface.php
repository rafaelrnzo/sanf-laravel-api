<?php

namespace Sanf\Core\Modules\Setting\Repositories;

interface FrequentlyAskQuestionRepositoryInterface
{
    public function query($specification);

    public function size($specification);

    public function findById($id);

    public function create($request);

    public function update($id, $request);

    public function destroy($id);

    public function queryCategory($specification);

    public function sizeCategory($specification);

    public function findCategoryById($id);

    public function createCategory($request);

    public function updateCategory($id, $request);

    public function destroyCategory($id);
}
