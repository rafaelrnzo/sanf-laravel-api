<?php

namespace Sanf\Core\Modules\Contract\Repositories;

interface ESignRepositoryInterface
{
    public function findUserById(int $id);

    public function findUserBySanfId(string $id);

    public function findUserByEmail(string $email);

    public function createUser(array $data);

    public function updateUser(int $id, array $data);

    public function findDocumentById(int $id);

    public function findDocumentByDocId(string $documentId);

    public function createDocument(array $data);

    public function updateDocument(int $id, array $data);

    public function documentAssigneeQuery($specification);

    public function documentAssigneeSize($specification = null);

    public function findDocumentAssigneeById(int $id);

    public function findDocumentAssigneeByDocId(int $userId, string $documentId);

    public function createDocumentAssignee(array $data);

    public function updateDocumentAssignee(int $id, array $data);
}
