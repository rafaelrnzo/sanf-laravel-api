<?php

namespace Sanf\Core\Modules\Survey\Models;

use NbsPhp\Core\Models\AbstractModel;

class SurveyModel extends AbstractModel
{
    public $table = 'survey';

    protected $fillable = [
        'xid',
        'branch_id',
        'contract_no',
        'profile_xid',
        'customer_name',
        'pic_name',
        'project_name',
        'project_location',
        'segment',
    ];

    public function surveyItems()
    {
        return $this->hasMany(SurveyItemModel::class, 'survey_id', 'id');
    }
}
