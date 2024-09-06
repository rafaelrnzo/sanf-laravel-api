<?php

namespace Sanf\Core\Modules\RequestedDocument\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\RequestedDocument\Models\RequestedDocumentItemEncryptedModel;

class EloquentRequestedDocumentItemEncryptedRepository extends AbstractEloquentRepository implements
    RequestedDocumentItemRepositoryInterface
{
    private RequestedDocumentItemEncryptedModel $model;
    private $encryptedFields;
    private $encryptedJsonFields;

    public function __construct(RequestedDocumentItemEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'document_name',
        ];
        $this->encryptedJsonFields = [
            'document_file',
        ];
    }

    public function create(array $request)
    {
        return $this->model->newQuery()->forceCreate($this->encryptBeforeCreate($request));
    }

    private function encryptBeforeCreate(array $data): array
    {
        $encryptor = SodiumEncryption::encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
                continue;
            }

            if (in_array($key, $this->encryptedJsonFields)) {
                $data[$key] = $encryptor->encryptForJson($value);
                continue;
            }
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }

    public function findByRequestIdAndDocNo(string $requestDocId, string $documentNo)
    {
        return $this->model->newQuery()
            ->whereNull('deleted_at')
            ->where('requested_document_id', '=', $requestDocId)
            ->where('document_id', '=', $documentNo)
            ->first();
    }

    public function update(int $id, array $request)
    {
        return $this->model->newQuery()
            ->where('id', '=', $id)
            ->update($this->encryptBeforeUpdate($request, $id));
    }

    private function encryptBeforeUpdate(array $data, $id): array
    {
        $model = $this->model->newQuery()->find($id);

        $encryptor = $model->encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
                continue;
            }

            if (in_array($key, $this->encryptedJsonFields)) {
                $data[$key] = $encryptor->encryptForJson($value);
                continue;
            }
        }

        return $data;
    }

    public function getByDocumentNoAndId(string $user_id, string $request_no, string $document_id)
    {
        return $this->model->newQuery()
            ->select('requested_document_item.*')
            ->join('requested_document', function ($query) {
                return $query->on('requested_document_item.requested_document_id', '=', 'requested_document.id')
                    ->whereNull('requested_document.deleted_at');
            })
            ->where('requested_document.user_id', $user_id)
            ->where('requested_document.request_no', $request_no)
            ->where('requested_document_item.document_id', $document_id)
            ->whereNotNull('requested_document_item.document_file')
            ->whereNull('requested_document_item.deleted_at')
            ->get();
    }
}
