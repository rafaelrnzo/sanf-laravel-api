<?php

namespace Sanf\Integration\Modules\StandbyFinancing;

use GuzzleHttp\Client;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Modules\StandbyFinancing\Payloads\SbfCheckInvoicePayload;
use Sanf\Integration\Modules\StandbyFinancing\Payloads\SbfSubmitPengajuanPayload;

class SanfApiService
{
    /** @var Client */
    private $client;

    private ?string $userId = null;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? app(Client::class);
    }

    public function setUser(?string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getPlafond(string $custId): array
    {
        return $this->send(
            Request::route('sanf-internal.sbf.plafond-header', $this->client)
                ->pathParams(['custId' => $custId])
        );
    }

    public function getPlafondListSbf(string $custId): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.plafond.list', $this->client)
                ->queryParams(['cust_id' => $custId])
        );
    }

    public function getPlafondDetailSbf(string $noPlafond): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.plafond.detail', $this->client)
                ->pathParams(['noPlafond' => $noPlafond])
        );
    }

    public function getBankAccount(string $custId): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.bank-account', $this->client)
                ->queryParams(['cust_id' => $custId])
        );
    }

    public function getDocumentList(string $custId): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.documents', $this->client)
                ->queryParams(['cust_id' => $custId])
        );
    }

    public function getListPencairan(string $custId, int $page = 1): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.pencairan.list', $this->client)
                ->queryParams(['cust_id' => $custId, 'page' => $page])
        );
    }

    public function getDetailPencairan(string $recapIdB2b): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.pencairan.detail', $this->client)
                ->pathParams(['recapId' => $recapIdB2b])
        );
    }

    public function checkInvoice(array $payload): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.check-invoice', $this->client)
                ->json(SbfCheckInvoicePayload::fromValidated($payload)->toArray())
        );
    }

    public function submitPengajuan(array $payload): array
    {
        return $this->send(
            Request::route('sanf-internal-v2.sbf.submit', $this->client)
                ->json(SbfSubmitPengajuanPayload::fromValidated($payload)->toArray())
        );
    }

    private function send(Request $request): array
    {
        if ($auth = $this->customAuth()) {
            $request->headers(['Authorization' => $auth]);
        }

        return $request->send()->json();
    }

    private function customAuth(): ?string
    {
        if (empty($this->userId)) {
            return null;
        }

        $clientId = config('sanf-api-v2.client_id');
        $clientSecret = config('sanf-api-v2.client_secret');

        return 'Basic ' . base64_encode(implode(';', [$clientId, $clientSecret, $this->userId]));
    }
}
