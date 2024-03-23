<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentItemRepositoryInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class BrowseUploadRequestedDocumentService implements ApplicationServiceInterface
{
    private SanfCoreApiClient $internalApiClient;
    private RequestedDocumentItemRepositoryInterface $eloquentRequestedDocItemRepository;

    public function __construct(
        SanfCoreApiClient $internalApiClient,
        RequestedDocumentItemRepositoryInterface $eloquentRequestedDocItemRepository
    ) {
        $this->internalApiClient = $internalApiClient;
        $this->eloquentRequestedDocItemRepository = $eloquentRequestedDocItemRepository;
    }

    /**
     * @param $dto
     * @return array
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        try {
            $dataCore = $this->internalApiClient->browseRequestedUploadDocuments($dto);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }

        $dataCoreFilterByDocumentId = array_filter($dataCore->data, function ($data) use ($dto) {
            return $data->DOC_ID == $dto->document_id;
        });

        $dataCoreMapping = array_map(function ($item) {
            return [
                'filename' => $item->FILENAME ?? null,
                'upload_at' => ($item->DT_UPLOAD) ? Carbon::make($item->DT_UPLOAD) : null,
            ];
        }, $dataCoreFilterByDocumentId);

        $dataDb = $this->eloquentRequestedDocItemRepository->getByDocumentNoAndId(
            $dto->user_id,
            $dto->request_id,
            $dto->document_id
        );

        $dataDbMapping = array_map(function ($item) {
            return [
                'filename' => $item['document_file']->origin,
                'upload_at' => Carbon::make($item['created_at']),
            ];
        }, $dataDb->toArray());

        $data = array_merge($dataCoreMapping, $dataDbMapping);

        // TODO remove laravel collection
        switch ($dto->sort_by) {
            case 'oldest':
                return collect($data)->sortBy('upload_at')->toArray();
                break;
            case 'earliest':
            default:
                return collect($data)->sortByDesc('upload_at')->toArray();
        }
    }
}
