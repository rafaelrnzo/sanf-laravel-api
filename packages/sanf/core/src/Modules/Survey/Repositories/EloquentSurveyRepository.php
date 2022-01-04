<?php

namespace Sanf\Core\Modules\Survey\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Survey\Entities\SurveyEntityFactoryInterface;
use Sanf\Core\Modules\Survey\Models\SurveyItemModel;
use Sanf\Core\Modules\Survey\Models\SurveyModel;

class EloquentSurveyRepository extends AbstractEloquentRepository implements SurveyRepositoryInterface
{
    private SurveyModel $surveyModel;
    private SurveyItemModel $surveyItemModel;
    private SurveyEntityFactoryInterface $entityFactory;

    public function __construct(
        SurveyModel $surveyModel,
        SurveyItemModel $surveyItemModel,
        SurveyEntityFactoryInterface $entityFactory
    ) {
        $this->surveyModel = $surveyModel;
        $this->surveyItemModel = $surveyItemModel;
        $this->entityFactory = $entityFactory;
    }

    public function add($fields)
    {
        $transaction = DB::transaction(function () use ($fields) {
            $surveyInput = collect($fields)->except('items')->toArray();
            $model = $this->surveyModel
                ->newQuery()
                ->forceCreate($surveyInput);

            foreach ($fields['items'] as $surveyItemInput) {
                $surveyItemInput['survey_id'] = $model->id;
                $this->surveyItemModel
                    ->newQuery()
                    ->forceCreate($surveyItemInput);
            }

            return $model;
        });

        return $this->entityFactory->make($transaction->toArray());
    }
}
