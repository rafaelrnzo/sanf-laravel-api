<?php

namespace Sanf\Api\Modules\Survey\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Guard;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Survey\Transformers\AssigneeSurveyTransformer;
use Sanf\Core\Modules\Survey\Dtos\PaginateAssigneeSurveyDto;
use Sanf\Core\Modules\Survey\Services\GetListAssigneeSurveyService;
use Spatie\Fractal\Fractal;

class SurveyAssignmentByUserController extends RestApiController
{
    /**
     * @param Guard $auth
     * @param GetListAssigneeSurveyService $service
     * @return Fractal
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function browse(Guard $auth, GetListAssigneeSurveyService $service)
    {
        $dto = new PaginateAssigneeSurveyDto(['userId' => $auth->id()]);

        $result = $service->execute($dto);

        return fractal($result->data, AssigneeSurveyTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
