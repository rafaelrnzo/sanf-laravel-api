<?php

namespace Sanf\Core\Modules\Ocr\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
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

        // $response = $this->coreClient->getOCRPermission($profile->email);
        $response = json_decode('{"status":true,"code":"S_GetData","message":"Success","data":{"EMAIL":"muflihtest@gmail.com","PERMISSION":true}}', true);

        return (object) [
            'xid' => $profile->xid,
            'email' => $response['data']['EMAIL'] ?? $profile->email,
            'isPermitted' => $response['data']['PERMISSION'] ?? false,
        ];
    }
}
