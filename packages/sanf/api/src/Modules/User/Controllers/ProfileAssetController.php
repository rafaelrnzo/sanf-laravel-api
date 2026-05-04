<?php

namespace Sanf\Api\Modules\User\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Asset\PublicAssetFileSimpleTransformer;
use Sanf\Core\Modules\Asset\AssetTypeEnum;
use Sanf\Core\Modules\Asset\AssetUploadRequestDto;
use Sanf\Core\Modules\Asset\UploadAssetService;
use Sanf\Core\Modules\Financing\Dto\UploadFinancingDocumentDto;
use Sanf\Core\Modules\Financing\Services\UploadFinancingDocumentService;

final class ProfileAssetController extends RestApiController
{
    public function postUpload(
        $xid,
        Request $request,
        UploadAssetService $assetService,
        UploadFinancingDocumentService $financingDocumentService
    ) {
        $input = $this->validate($request, [
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png', 'mimetypes:image/png,image/jpeg,image/jpg,image/svg', 'max:5000'],
            'asset_type' => [
                'required',
                Rule::in(AssetTypeEnum::ASSET_TYPE),
            ],
        ]);
        if (in_array($input['asset_type'], AssetTypeEnum::PROFILE_DOCUMENT)) {
            $dto = new UploadFinancingDocumentDto([
                'xid' => $xid,
                'file' => $input['file'],
                'asset_type' => ((int) $input['asset_type'] === AssetTypeEnum::ID_KTP) ?
                    AssetTypeEnum::KTP : AssetTypeEnum::NPWP,
            ]);

            $financingDocumentService->execute($dto);

            return $this->responseOk();
        }

        // set upload file dto;
        $dto = new AssetUploadRequestDto([
            'file' => $input['file'],
            'type' => (int) $input['asset_type'],
        ]);

        $result = $assetService->execute($dto);

        return fractal($result, PublicAssetFileSimpleTransformer::class);
    }
}
