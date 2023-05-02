<?php

namespace Sanf\Core\Modules\RequestedDocument\Repositories;

interface RequestedDocumentItemRepositoryInterface
{

    public function getByDocumentNoAndId(string $user_id, string $request_no, string $document_id);

    public function create(array $request);
}
