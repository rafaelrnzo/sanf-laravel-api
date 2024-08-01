<?php

namespace Sanf\Integration\Modules\AdIns;

use GuzzleHttp\Client;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Modules\AdIns\DTOs\DocumentDto;
use Sanf\Integration\Modules\AdIns\DTOs\OneTimePasswordDto;
use Sanf\Integration\Modules\AdIns\DTOs\RegistrationDto;

class AdInsESignApiClient
{
    public const SUCCESS_CODE = 0;

    protected $client;

    public function __construct()
    {
        $this->client = app(Client::class);
    }

    private function request(string $route, array $requestData = [])
    {
        $tenantCode = config('ad-ins.e-sign-hub.tenant_code');
        $requestData['audit'] = [
            'callerId' => "USER{$tenantCode}",
        ];

        $request = Request::route($route, $this->client)
            ->json($requestData);

        return $request->send();
    }

    public function registerCheckByIdentityNumber(RegistrationDto $dto)
    {
        $requestData = [
            'dataType' => 'NIK',
            'userData' => $dto->identityNumber,
        ];
        $response = $this->request('user.register.check', $requestData);

        return $response->json(false);
    }

    public function registerCheckByEmail(RegistrationDto $dto)
    {
        $requestData = [
            'dataType' => 'EMAIL',
            'userData' => $dto->email,
        ];
        $response = $this->request('user.register.check', $requestData);

        return $response->json(false);
    }

    public function registerCheckByMsisdn(RegistrationDto $dto)
    {
        $requestData = [
            'dataType' => 'PHONE',
            'userData' => $dto->msisdn,
        ];
        $response = $this->request('user.register.check', $requestData);

        return $response->json(false);
    }

    public function register(RegistrationDto $dto)
    {
        $requestData = [
            'nama' => $dto->fullName,
            'email' => $dto->email,
            'tmpLahir' => $dto->birthPlace,
            'tglLahir' => $dto->birthOfDate,
            'jenisKelamin' => $dto->gender,
            'tlp' => $dto->msisdn,
            'idKtp' => $dto->identityNumber,
            'alamat' => $dto->address,
            'provinsi' => $dto->province,
            'kota' => $dto->city,
            'kecamatan' => $dto->district,
            'kelurahan' => $dto->subDistrict,
            'kodePos' => $dto->postalCode,
            'selfPhoto' => $dto->selfPhoto,
            'idPhoto' => $dto->identityCardPhoto,
            'password' => $dto->password,
            'psreCode' => config('ad-ins.e-sign-hub.psre_code'),
        ];
        $response = $this->request('user.register.submit', $requestData);

        return $response->json(false);
    }

    public function requestOTP(OneTimePasswordDto $dto)
    {
        $requestData = [
            'phoneNo' => $dto->msisdn,
            'email' => $dto->email,
            'refNumber' => $dto->referenceNo,
        ];
        $response = $this->request('user.otp.request', $requestData);

        return $response->json(false);
    }

    public function signDocument(DocumentDto $dto)
    {
        $requestData = [
            'documentId' => $dto->documentsId,
            'email' => $dto->email,
            'phoneNo' => $dto->msisdn,
            'password' => $dto->password,
            'ipAddress' => $dto->ip,
            'browserInfo' => $dto->browser,
            'otp' => $dto->otp,
            'selfPhoto' => $dto->selfPhoto,
        ];
        $response = $this->request('document.sign.submit', $requestData);

        return $response->json(false);
    }

    public function signDocumentCheck(DocumentDto $dto)
    {
        $requestData = [
            'refNo' => $dto->referenceNo,
        ];
        $response = $this->request('document.sign.check', $requestData);

        return $response->json(false);
    }

    public function signDocumentDownload(DocumentDto $dto)
    {
        $requestData = [
            'documentId' => $dto->documentId,
        ];
        $response = $this->request('document.sign.download', $requestData);

        return $response->json(false);
    }
}
