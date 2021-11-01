<?php

namespace Sanf\Api\Modules\Financing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Financing\Dto\UploadFinancingDocumentDto;
use Sanf\Core\Modules\Financing\FinancingDocumentAssetEnum;
use Sanf\Core\Modules\Financing\Services\UploadFinancingDocumentService;

class FinancingDocumentController extends RestApiController
{
    public function upload(
        $xid,
        Request $request,
        UploadFinancingDocumentService $service
    ) {
        $input = $this->validate($request, [
            'file' => ['required', 'image', 'mimetypes:image/png,image/jpeg,image/jpg,image/svg', 'max:5000'],
            'asset_type' => [
                'required',
                Rule::in([FinancingDocumentAssetEnum::ID_KTP, FinancingDocumentAssetEnum::ID_NPWP])
            ]
        ]);

        $dto = new UploadFinancingDocumentDto([
            'xid' => $xid,
            'file' => $input['file'],
            'asset_type' => ((int)$input['asset_type'] === FinancingDocumentAssetEnum::ID_KTP) ?
                FinancingDocumentAssetEnum::KTP : FinancingDocumentAssetEnum::NPWP,
        ]);

        $service->execute($dto);

        return $this->responseOk();
    }
}