<?php

namespace Sanf\Core\Modules\Survey\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Survey\Entities\SurveyEntitiesInterface;
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

    public function query($specification)
    {
        $models = $specification->buildQuery($this->surveyModel)->get();
        return $this->stripEloquentModel($models);
    }

    /**
     * @param array $fields
     * @return SurveyEntitiesInterface
     */
    public function add($fields): SurveyEntitiesInterface
    {
        [$transaction, $transactionItem] = DB::transaction(function () use ($fields) {
            $surveyInput = collect($fields)->except('items')->toArray();
            $survey = $this->surveyModel
                ->newQuery()
                ->forceCreate($surveyInput);

            foreach ($fields['items'] as $surveyItemInput) {
                $surveyItemInput['survey_id'] = $survey->id;
                $this->surveyItemModel
                    ->newQuery()
                    ->forceCreate($surveyItemInput);
            }

            return [$survey, $survey->surveyItems];
        });

        return $this->entityFactory->make($transaction->toArray());
    }
}
