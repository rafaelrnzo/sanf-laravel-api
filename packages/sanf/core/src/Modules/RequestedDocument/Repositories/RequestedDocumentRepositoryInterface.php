<?php

namespace Sanf\Core\Modules\RequestedDocument\Repositories;

interface RequestedDocumentRepositoryInterface
{
    public function query($specification);

    public function count($specification): int;

    public function create(array $request);

    public function update(int $id, array $request);

    public function findByRequestNo(string $request_no, string $profile_xid);

    public function incrementTotalUploaded(int $id);
}
