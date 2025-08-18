<?php

namespace Sanf\Api\Modules\PdcHold\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\PdcHold\Transformers\MyPdcHoldSubmissionSimpleTransformer;
use Sanf\Api\Modules\PdcHold\Transformers\MyPdcHoldSubmissionTransformer;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldMultiContractByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldMultiGiroByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\BrowsePdcHoldSubmissionByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\PdcHoldMultiContractRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\PdcHoldMultiGiroRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\PdcHoldReasonRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\ReadPdcHoldSubmissionByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\ResumePdcByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Services\AddPdcHoldMultiContractByUserService;
use Sanf\Core\Modules\PdcHold\Services\AddPdcHoldMultiGiroByUserService;
use Sanf\Core\Modules\PdcHold\Services\BrowsePdcHoldSubmissionByUserService;
use Sanf\Core\Modules\PdcHold\Services\ReadPdcHoldSubmissionByUserService;
use Sanf\Core\Modules\PdcHold\Services\ResumePdcByUserService;

/**
 * @since CR2025
 */
class PdcHoldController extends RestApiController
{
    public function getBrowse(
        Guard $auth,
        Request $request,
        BrowsePdcHoldSubmissionByUserService $service,
        string $xid
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'status_id' => ['nullable', 'integer', Rule::in(PdcHoldStatusEnum::ALL_STATUS)],
            'type' => ['nullable', 'integer', Rule::in(PdcHoldTypeEnum::ALL_TYPES)],
            'resumable' => ['nullable', 'boolean'],
        ]);

        $dto = new BrowsePdcHoldSubmissionByUserRequestDto($input + [
            'userId' => $auth->id(),
            'profileXid' => $xid,
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, new MyPdcHoldSubmissionSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getRead(
        Guard $auth,
        ReadPdcHoldSubmissionByUserService $service,
        string $xid,
        string $submissionXid
    ) {
        $dto = new ReadPdcHoldSubmissionByUserRequestDto([
            'profileXid' => $xid,
            'xid' => $submissionXid,
            'userId' => $auth->id(),
        ]);
        $result = $service->execute($dto);

        return fractal($result, new MyPdcHoldSubmissionTransformer());
    }

    public function postAddMultiGiro(
        Guard $auth,
        Request $request,
        AddPdcHoldMultiGiroByUserService $service,
        string $xid
    ) {
        $input = $this->validate($request, [
            'date_start' => ['required', 'string', 'date_format:Y-m-d'],
            'date_end' => ['required', 'string', 'date_format:Y-m-d'],
            'contract_no' => ['required', 'string'],
            'pdc_hold' => ['required', 'array'],
            'pdc_hold.*.pdc_no' => ['required', 'string'],
            'pdc_hold.*.amount' => ['required', 'numeric'],
            'pdc_hold.*.currency_type' => ['nullable', 'in:IDR,USD'],
            'pdc_hold.*.date' => ['required', 'date_format:Y-m-d'],
            'pdc_hold.*.pdc_type' => ['nullable', 'string'],
            'reason' => ['required', 'array'],
            'reason.id' => ['required', 'integer'],
            'reason.value' => ['required', 'string'],
        ]);

        $multiGiro = array_map(function ($item) {
            $item['currency_type'] ??= 'IDR';

            return new PdcHoldMultiGiroRequestDto($item);
        }, $input['pdc_hold']);

        $dto = new AddPdcHoldMultiGiroByUserRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'dateStart' => CarbonImmutable::createFromFormat('Y-m-d', $input['date_start']),
            'dateEnd' => CarbonImmutable::createFromFormat('Y-m-d', $input['date_end']),
            'contractNo' => $input['contract_no'],
            'multiGiro' => $multiGiro,
            'reason' => new PdcHoldReasonRequestDto((array) $input['reason']),
        ]);

        $result = $service->execute($dto);

        return $this->responseOk('Success', [
            'xid' => $result->xid,
        ]);
    }

    public function postAddMultiContract(
        Guard $auth,
        Request $request,
        AddPdcHoldMultiContractByUserService $service,
        string $xid
    ) {
        $input = $this->validate($request, [
            'period' => ['required', 'string', 'date_format:Y-m'],
            'pdc_hold' => ['required', 'array'],
            'pdc_hold.*.pdc_no' => ['required', 'string'],
            'pdc_hold.*.amount' => ['required', 'numeric'],
            'pdc_hold.*.currency_type' => ['nullable', 'in:IDR,USD'],
            'pdc_hold.*.date' => ['required', 'date_format:Y-m-d'],
            'pdc_hold.*.pdc_type' => ['nullable', 'string'],
            'pdc_hold.*.contract_no' => ['required', 'string'],
            'reason' => ['required', 'array'],
            'reason.id' => ['required', 'integer'],
            'reason.value' => ['required', 'string'],
        ]);

        $multiContract = array_map(function ($item) {
            $item['currency_type'] ??= 'IDR';

            return new PdcHoldMultiContractRequestDto($item);
        }, $input['pdc_hold']);

        $dto = new AddPdcHoldMultiContractByUserRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'period' => CarbonImmutable::createFromFormat('Y-m', $input['period'])->firstOfMonth(),
            'multiContract' => $multiContract,
            'reason' => new PdcHoldReasonRequestDto((array) $input['reason']),
        ]);

        $result = $service->execute($dto);

        return $this->responseOk('Success', [
            'xid' => $result->xid,
        ]);
    }

    public function postResume(
        Guard $auth,
        Request $request,
        ResumePdcByUserService $service,
        string $xid
    ) {
        $input = $this->validate($request, [
            'giro_xids' => ['required', 'array'],
            'giro_xids.*' => ['required', 'string', 'max:32'],
        ]);

        $dto = new ResumePdcByUserRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'giroXids' => $input['giro_xids'],
        ]);

        $result = $service->execute($dto);

        return $this->responseOk('Success', $result->all());
    }
}
