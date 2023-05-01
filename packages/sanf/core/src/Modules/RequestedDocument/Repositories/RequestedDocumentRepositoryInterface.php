<?php

namespace Sanf\Core\Modules\RequestedDocument\Repositories;

interface RequestedDocumentRepositoryInterface
{

    public function query($specification);

    public function count($specification): int;

    public function create(array $request);
}
