<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\GetESignUserResponseDto;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Enums\TekenAjaApiResponseErrorCodeEnum;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;
use Sanf\Integration\Exceptions\TekenAjaInvalidParameterRegistrationException;
use Sanf\Integration\InternalApiClient;
use Sanf\Integration\TekenAjaInternalApiClient;

final class GetESignUserService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected InternalApiClient $client;
    protected TekenAjaInternalApiClient $clientTekenAja;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        AuthModel $userRepository,
        InternalApiClient $client,
        TekenAjaInternalApiClient $clientTekenAja
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userRepository = $userRepository;
        $this->client = $client;
        $this->clientTekenAja = $clientTekenAja;
    }

    /**
     * @param null $dto
     * @return object
     * @throws UserNotFoundException
     */
    public function execute($dto = null): object
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $response = $this->client->getAvailableESignUser($user->username);
        $result = array_map(function ($item) use ($user) {
            return [
                'email' => isset($item['EMAIL']) ? $item['EMAIL'] : null,
                'msisdn' => isset($item['MOBILE']) ? $item['MOBILE'] :  $user->phone_number,
                'nik' => isset($item['NIK']) ? $item['NIK'] : null,
                'fullName' => isset($item['NAME']) ? $item['NAME'] : null,
                'dob' => isset($item['DOB']) ? $item['DOB'] : null,
                'pob' => isset($item['POB']) ? $item['POB'] : null,
                'gender' => isset($item['GENDER']) ? (int)$item['GENDER'] : null,
                'address' => isset($item['ADDRESS']) ? $item['ADDRESS'] : null,
                'postalCode' => isset($item['ZIP_CODE']) ? (int)$item['ZIP_CODE'] : null,
                'statusId' => ESignRegistrationStatusEnum::AVAILABLE,
            ];
        }, $response['data'])[0];

        $userTekenAja = $this->eSignRepository->findUserByEmail($result['email']);
        $resultTekenAja = $this->clientTekenAja->registerCheck([
            ['name' => 'email', 'contents' => $result['email'],],
            ['name' => 'nik', 'contents' => $result['nik'],],
        ]);

        $exceptCodeCondition = ($resultTekenAja['code'] == TekenAjaApiResponseErrorCodeEnum::USER_EXISTS_VERIFIED or $resultTekenAja['code'] == TekenAjaApiResponseErrorCodeEnum::NIK_EMAIL_MATCHED);
        if ($resultTekenAja['code'] and !$exceptCodeCondition and strtolower($resultTekenAja['status']) != 'ok') {
            if ($resultTekenAja['code'] == TekenAjaApiResponseErrorCodeEnum::USER_DO_NOT_EXISTS) {
                if ($userTekenAja and ($userTekenAja->email == $result['email'] and $userTekenAja->nik == $result['nik'])) {
                    $result['statusId'] = ESignRegistrationStatusEnum::AVAILABLE;
                }
            }

            $this->errorHandle($resultTekenAja['code'], $resultTekenAja['message'], $result['email'], $result['nik']);
        } else {
            if (!$userTekenAja) {
                $request['xid'] = nano_id();
                $request['email'] = $result['email'];
                $request['nik'] = $result['nik'];
                $request['user_id'] = $user->id;
                $request['status_id'] = ESignRegistrationStatusEnum::COMPLETE;
                $request['total_submit_registration'] = 1;
                $request['created_at'] = Carbon::now();
                $userTekenAja = $this->eSignRepository->createUser($request);
            }
            $result['statusId'] = $userTekenAja->status_id;
        }

        if ($userTekenAja) {
            $result = $this->mapping($result, $userTekenAja);
        }

        return new GetESignUserResponseDto($result);
    }

    private function mapping(array $data, object $regression): array
    {
        return [
            'xid' => $regression->xid ?? null,
            'registrationId' => null,
            'email' => $regression->email ?? $data['email'],
            'msisdn' => $regression->msisdn ?? $data['msisdn'],
            'nik' => $regression->nik ?? $data['nik'],
            'fullName' => $regression->full_name ?? $data['fullName'],
            'dob' => ($regression->dob) ? Carbon::createFromFormat('Y-m-d', $regression->dob)->format(
                'd/m/Y'
            ) : $data['dob'],
            'pob' => $regression->pob ?? $data['pob'],
            'gender' => $regression->gender ?? $data['gender'],
            'address' => $regression->address ?? $data['address'],
            'postalCode' => $regression->postal_code ?? $data['postalCode'],
            'provinceId' => $regression->province_id ?? null,
            'districtId' => $regression->district_id ?? null,
            'subDistrictId' => $regression->sub_district_id ?? null,
            'selfieFile' => $regression->selfie_file ?? null,
            'identityFile' => $regression->identity_file ?? null,
            'statusId' => $data['statusId'],
            'createdAt' => ($regression->created_at) ? Carbon::parse($regression->created_at) : null,
            'updatedAt' => ($regression->updated_at) ? Carbon::parse($regression->updated_at) : null,
        ];
    }

    private function errorHandle(string $code, $messages, string $email, string $nik)
    {
        switch ($code) {
            case TekenAjaApiResponseErrorCodeEnum::INVALID_PARAMETER:
                $response = array_map(function ($item) {
                    return $item[0];
                }, $messages);
                throw new TekenAjaInvalidParameterRegistrationException(implode('|', $response));
                break;
            case TekenAjaApiResponseErrorCodeEnum::USER_DO_NOT_EXISTS:
            case TekenAjaApiResponseErrorCodeEnum::USER_EXISTS_UNVERIFIED:
            case TekenAjaApiResponseErrorCodeEnum::USER_EXISTS_CERTIFICATE_EXPIRED:
            case TekenAjaApiResponseErrorCodeEnum::NIK_EMAIL_UNMATCH:
                Log::warning("Email: {$email} and NIK {$nik} : {$messages}");
                break;
            case TekenAjaApiResponseErrorCodeEnum::SYSTEM_FAILURE:
            default:
                throw new TekenAjaExternalApiException($messages);
        }
    }
}
