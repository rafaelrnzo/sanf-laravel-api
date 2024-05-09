<?php

namespace Sanf\Core\Modules\Ocr\Services;

use Exception;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Ocr\Exceptions\UserOcrPermissionNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class GetUserOCRPermissionService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $coreClient;

    public function __construct(SanfCoreApiClient $coreClient)
    {
        $this->coreClient = $coreClient;
    }

    public function execute($request = null)
    {
        try {
            $response = $this->coreClient->findCustomerById($request->customerId);
            $profile = collect($response['data'])
                ->map(function ($item) {
                    return (object) [
                        'xid' => $item['CUST_ID_SANF'],
                        'title' => $item['COMPANY_TYPE'],
                        'fullName' => $item['IDENTITY_NAME'],
                        'picName' => $item['PIC_NAME'],
                        'email' => $item['EMAIL_ADDR'],
                    ];
                })->first();
        } catch (Exception $exception) {
            throw new UserOcrPermissionNotFoundException();
        }

        $account = ['F_SCANOCR' => 'N'];
        try {
            $response = $this->coreClient->getOCRPermission($profile->email);
            $accountFiltered = array_filter($response['data'], function ($user) use ($request) {
                return $user['CUST_ID'] === $request->customerId;
            });
            $accountFiltered = array_values($accountFiltered);

            if (empty($accountFiltered) === false) {
                $account = $accountFiltered[0];
            }
        } catch (Exception $exception) {
            report($exception);
        }

        return (object) [
            'xid' => $profile->xid,
            'email' => $account['EMAIL'] ?? $profile->email,
            'isPermitted' => $account['F_SCANOCR'] == 'Y',
        ];
    }
}
