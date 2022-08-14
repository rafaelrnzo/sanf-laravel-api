<?php

namespace Sanf\Core\Modules\Setting\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;

class FrequentlyAskQuestionModel extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'faq';

    protected $fillable = [
        'title',
        'description',
        'is_popular',
        'order',
    ];

    public function category()
    {
        return $this->hasOne(FrequentlyAskQuestionCategoryModel::class, 'id', 'category_id');
    }
}
