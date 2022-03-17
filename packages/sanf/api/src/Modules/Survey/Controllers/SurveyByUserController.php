<?php

namespace Sanf\Api\Modules\Survey\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Survey\Transformers\GetDetailSurveyResponseTransformer;
use Sanf\Api\Modules\Survey\Transformers\GetListFinishedSurveyResponseTransformer;
use Sanf\Api\Modules\Survey\Transformers\GetListSurveyResponseTransformer;
use Sanf\Core\Modules\Survey\Dtos\AddSurveySubmissionRequestDto;
use Sanf\Core\Modules\Survey\Dtos\GetListSurveyRequestDto;
use Sanf\Core\Modules\Survey\Services\AddSurveySubmissionService;
use Sanf\Core\Modules\Survey\Services\GetDetailSurveyByUserService;
use Sanf\Core\Modules\Survey\Services\GetListSurveyService;

class SurveyByUserController extends RestApiController
{
    public function browse(
        Guard $auth,
        Request $request,
        GetListSurveyService $service
    ) {
        $input = $this->validate($request, [
                'status_id' => ['nullable', 'integer', 'in:0,2'],
                'skip' => ['nullable', 'integer', 'max:2147483647'],
                'limit' => ['nullable', 'integer', 'max:2147483647'],
                'sort_by' => ['nullable', 'in:earliest,latest'],
            ]
        );
        if ((int)($input['status_id'] ?? null) === 0) {
            $input['status_id'] = null;
        }
        $dto = new GetListSurveyRequestDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);
        if (is_null($input['status_id'])) {
            return fractal($result->data, GetListFinishedSurveyResponseTransformer::class)
                ->paginateWith(new LazyPaginatorAdapter($result->paginate));
        }

        return fractal($result->data, GetListSurveyResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function add(Request $request, AddSurveySubmissionService $service)
    {
        $input = $this->validate(
            $request,
            [
                'profile_xid' => ['required', 'string', 'max:255'],
                'branch_id' => ['required', 'string', 'max:255'],
                'contract_no' => ['required', 'string', 'max:255'],
                'pic_name' => ['nullable', 'string', 'max:255'],
                'customer_name' => ['nullable', 'string', 'max:255'],
                'project_name' => ['nullable', 'string', 'max:255'],
                'segment' => ['nullable', 'string', 'max:255'],
                'items' => ['required', 'array'],
                'items.*.code' => ['required', 'string', 'max:255'],
                'items.*.title' => ['required', 'string', 'max:255'],
                'items.*.description' => ['required', 'string', 'max:65535'],
                'items.*.image_files' => ['required', 'array'],
                'items.*.image_files.*' => ['required', 'string'],
            ]
        );

        $dto = new AddSurveySubmissionRequestDto($input);

        $service->execute($dto);

        return $this->responseOk();
    }

    public function detail(
        $contract_no,
        Guard $auth,
        Request $request,
        GetDetailSurveyByUserService $service
    ) {
        $input = $this->validate(
            $request,
            [
                'skip' => ['nullable', 'integer', 'max:2147483647'],
                'limit' => ['nullable', 'integer', 'max:2147483647'],
                'sort_by' => ['nullable', 'in:earliest,latest'],
            ]
        );

        $dto = new GetListSurveyRequestDto(
            $input + [
                'userId' => $auth->id(),
                'contractNo' => $contract_no,
            ]
        );

        $result = $service->execute($dto);

        return fractal($result->data, GetDetailSurveyResponseTransformer::class);
    }
}
