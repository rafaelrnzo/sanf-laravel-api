<?php

namespace Sanf\Core\Modules\Survey\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Survey\Entities\SurveyEntitiesInterface;
use Sanf\Core\Modules\Survey\Entities\SurveyEntityFactoryInterface;
use Sanf\Core\Modules\Survey\Models\SurveyEncryptedModel;
use Sanf\Core\Modules\Survey\Models\SurveyItemModel;

class EloquentSurveyEncryptedRepository extends AbstractEloquentRepository implements SurveyRepositoryInterface
{
    protected $encryptedFields;

    public function __construct(
        SurveyEncryptedModel $surveyModel,
        SurveyItemModel $surveyItemModel,
        SurveyEntityFactoryInterface $entityFactory
    ) {
        $this->surveyModel = $surveyModel;
        $this->surveyItemModel = $surveyItemModel;
        $this->entityFactory = $entityFactory;

        $this->encryptedFields = [
            'customer_name',
            'pic_name',
        ];
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
                ->forceCreate($this->encryptBeforeCreate($surveyInput));

            $survey->refresh();

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

    private function encryptBeforeCreate(array $data): array
    {
        $encryptor = SodiumEncryption::encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
            }
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }
}
