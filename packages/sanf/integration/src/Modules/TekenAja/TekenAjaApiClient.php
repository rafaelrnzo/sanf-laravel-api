<?php

namespace Sanf\Integration\Modules\TekenAja;

use NbsPhp\ApiWrapper\Api\Request;

class TekenAjaApiClient
{
    const DEFAULT_SKIP = 0;
    const DEFAULT_LIMIT = 2147483647;
    const DEFAULT_ORDER = 'Latest';

    protected $client;

    public function __construct()
    {
        //TODO INJECT
        $this->client = app(\GuzzleHttp\Client::class);
    }

    /**
     * {.
        "11": "ACEH",
        "51": "BALI",
        "36": "BANTEN",
     }
     */
    public function getProvinces()
    {
        $response = Request::route('location.province', $this->client)->send();

        return $response->json();
    }

    /**
     * {.
        "73": "JAKARTA BARAT",
        "71": "JAKARTA PUSAT",
        "74": "JAKARTA SELATAN",
        "75": "JAKARTA TIMUR",
        "72": "JAKARTA UTARA",
        "1": "KEPULAUAN SERIBU"
     }
     */
    public function getDistricts(string $provinceId)
    {
        $response = Request::route('location.district', $this->client)
            ->queryParams(['province' => $provinceId])
            ->send();

        return $response->json();
    }

    public function getSubDistricts(string $provinceId, string $subDistrict)
    {
        $response = Request::route('location.subdistrict', $this->client)
            ->queryParams([
                'province' => $provinceId,
                'district' => $subDistrict,
            ])
            ->send();

        return $response->json();
    }

    public function addRegisterUser(array $request)
    {
        $response = Request::route('user.registration.detail', $this->client)
            ->headers(['Accept' => 'application/json'])
            ->multipart($request)
            ->send();

        return $response->json();
    }

    public function registerCheck(array $request)
    {
        $request[] = [
            'name' => 'action',
            'contents' => 'check_nik',
        ];
        $response = Request::route('user.registration.check', $this->client)
            ->headers(['Accept' => 'application/json'])
            ->multipart($request)
            ->send();

        return $response->json();
    }

    public function sendVerificationMail(array $request)
    {
        $request[] = [
            'name' => 'action',
            'contents' => 'resend_email',
        ];
        $response = Request::route('user.registration.check', $this->client)
            ->headers(['Accept' => 'application/json'])
            ->multipart($request)
            ->send();

        return $response->json();
    }

    public function generateSignUrl(string $documentId, string $email)
    {
        $response = Request::route('document.generate-url', $this->client)
            ->headers(['Accept' => 'application/json'])
            ->multipart([
                ['name' => 'document_id', 'contents' => $documentId],
                ['name' => 'user_email', 'contents' => $email],
            ])
            ->send();

        return $response->json();
    }

    public function download(string $documentId)
    {
        $response = Request::route('document.download', $this->client)
            ->headers(['Accept' => 'application/json'])
            ->multipart([
                ['name' => 'document_id', 'contents' => $documentId],
            ])
            ->send();

        return $response->json();
    }
}
