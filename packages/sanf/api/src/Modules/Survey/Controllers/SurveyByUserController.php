<?php

namespace Sanf\Api\Modules\Survey\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Survey\Transformers\GetListSurveyResponseTransformer;
use Sanf\Api\Modules\Survey\Transformers\GetDetailSurveyResponseTransformer;
use Sanf\Core\Modules\Survey\Dtos\AddSurveySubmissionRequestDTO;
use Sanf\Core\Modules\Survey\Dtos\GetListSurveyRequestDTO;
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
        $input = $this->validate(
            $request,
            [
                'status_id' => ['required', 'integer', 'in:1,2'],
                'skip' => ['nullable', 'integer', 'max:99'],
                'limit' => ['nullable', 'integer', 'max:99'],
                'sort_by' => ['nullable', 'in:earliest,latest'],
            ]
        );
        $dto = new GetListSurveyRequestDTO($input + ['userId' => $auth->id()]);

        $result = $service->execute($dto);

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
                'company_name' => ['nullable', 'string', 'max:255'],
                'customer_name' => ['nullable', 'string', 'max:255'],
                'project_name' => ['nullable', 'string', 'max:255'],
                'segment' => ['nullable', 'string', 'max:255'],
                'items' => ['required', 'array'],
                'items.*.code' => ['required', 'string', 'max:255'],
                'items.*.title' => ['required', 'string', 'max:255'],
                'items.*.description' => ['required', 'string', 'max:65535'],
                'items.*.image_files' => ['required', 'array'],
                'items.*.image_files.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5000'],
            ]
        );

        $dto = new AddSurveySubmissionRequestDTO($input);

        $result = $service->execute($dto);

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
                'skip' => ['nullable', 'integer', 'max:99'],
                'limit' => ['nullable', 'integer', 'max:99'],
                'sort_by' => ['nullable', 'in:earliest,latest'],
            ]
        );

        $dto = new GetListSurveyRequestDTO(
            $input + [
                'userId' => $auth->id(),
                'contractNo' => $contract_no,
            ]
        );

        $result = $service->execute($dto);

        return fractal($result->data, GetDetailSurveyResponseTransformer::class);
    }
}
