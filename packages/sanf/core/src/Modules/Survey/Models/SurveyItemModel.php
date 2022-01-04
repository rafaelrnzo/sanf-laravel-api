<?php

namespace Sanf\Core\Modules\Survey\Models;

use NbsPhp\Core\Models\AbstractModel;

class SurveyItemModel extends AbstractModel
{
    public $table = 'survey_item';

    protected $fillable = [
        'survey_id',
        'code',
        'title',
        'description',
        'image_files',
        'image_path',
    ];

    public function surveyItems()
    {
        return $this->belongsTo(SurveyModel::class, 'survey_id', 'id');
    }
}
