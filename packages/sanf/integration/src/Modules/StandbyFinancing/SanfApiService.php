<?php

namespace Sanf\Integration\Modules\StandbyFinancing;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class SanfApiService
{
    private Client $client;
    private string $mobileBaseUrl;
    private string $coreBaseUrl;
    private int $timeout;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? app(Client::class);
        $this->mobileBaseUrl = rtrim((string) config('services.sanf.base_url_mobile'), '/');
        $this->coreBaseUrl = rtrim((string) config('services.sanf.base_url_core'), '/');
        $this->timeout = (int) config('services.sanf.timeout', 30);
    }

    public function getPlafond(string $custId): array
    {
        return $this->get(
            $this->mobileUrl('/V2/plafond/' . rawurlencode($custId)),
            [],
            'get_plafond'
        );
    }

    public function getPlafondListSbf(string $custId): array
    {
        return $this->get($this->coreUrl('/plafond/list_sbf'), ['cust_id' => $custId], 'get_plafond_list_sbf');
    }

    public function getPlafondDetailSbf(string $noPlafond): array
    {
        return $this->get(
            $this->coreUrl('/plafond/detail_sbf/' . rawurlencode($noPlafond)),
            [],
            'get_plafond_detail_sbf'
        );
    }

    public function getBankAccount(string $custId): array
    {
        return $this->get($this->coreUrl('/standby_financing/bank_account'), ['cust_id' => $custId], 'get_bank_account');
    }

    public function getDocumentList(string $custId): array
    {
        return $this->get($this->coreUrl('/standby_financing/document'), ['cust_id' => $custId], 'get_document_list');
    }

    public function getListPencairan(string $custId, int $page = 1): array
    {
        return $this->get($this->coreUrl('/standby_financing/list'), [
            'cust_id' => $custId,
            'page' => $page,
        ], 'get_list_pencairan');
    }

    public function getDetailPencairan(string $recapIdB2b): array
    {
        return $this->get(
            $this->coreUrl('/standby_financing/detail/' . rawurlencode($recapIdB2b)),
            [],
            'get_detail_pencairan'
        );
    }

    public function checkInvoice(array $payload): array
    {
        $path = (string) config('services.sanf.paths.check_invoice', '/standby_financing/check_invoice');

        return $this->post($this->coreUrl($path), $payload, 'check_invoice');
    }

    public function submitPengajuan(array $payload): array
    {
        $path = (string) config('services.sanf.paths.submit_pengajuan', '/standby_financing/submit');

        return $this->post($this->coreUrl($path), $payload, 'submit_pengajuan');
    }

    private function get(string $url, array $query, string $operation): array
    {
        return $this->request('GET', $url, ['query' => $query], $operation);
    }

    private function post(string $url, array $payload, string $operation): array
    {
        return $this->request('POST', $url, ['json' => $payload], $operation);
    }

    private function request(string $method, string $url, array $options, string $operation): array
    {
        $options = array_merge($options, [
            'headers' => $this->headers(),
            'timeout' => $this->timeout,
            'connect_timeout' => $this->timeout,
        ]);

        Log::info('SBF core request', [
            'operation' => $operation,
            'method' => $method,
            'url' => $url,
            'payload' => $options['query'] ?? $options['json'] ?? [],
        ]);

        try {
            $response = $this->client->request($method, $url, $options);
            $data = $this->decode($response);

            Log::info('SBF core response', [
                'operation' => $operation,
                'status_code' => $response->getStatusCode(),
                'response' => $data,
            ]);

            return $data;
        } catch (\Throwable $exception) {
            Log::error('SBF core request failed', [
                'operation' => $operation,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function decode(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();
        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Core system returned an invalid JSON response.');
        }

        return $decoded;
    }

    private function headers(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if (app()->bound('request') && app('request')->header('X-Request-ID')) {
            $headers['X-Request-ID'] = app('request')->header('X-Request-ID');
        }

        $authorization = config('services.sanf.auth_token');

        if (!$authorization && app()->bound('request')) {
            $authorization = app('request')->header('Authorization');
        }

        if ($authorization) {
            $headers['Authorization'] = str_starts_with($authorization, 'Bearer ')
                || str_starts_with($authorization, 'Basic ')
                ? $authorization
                : 'Bearer ' . $authorization;
        }

        return $headers;
    }

    private function mobileUrl(string $path): string
    {
        if ($this->mobileBaseUrl === '') {
            throw new RuntimeException('SANF mobile base URL is not configured.');
        }

        return $this->mobileBaseUrl . '/' . ltrim($path, '/');
    }

    private function coreUrl(string $path): string
    {
        if ($this->coreBaseUrl === '') {
            throw new RuntimeException('SANF core base URL is not configured.');
        }

        return $this->coreBaseUrl . '/' . ltrim($path, '/');
    }
}
