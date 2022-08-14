<?php

namespace Sanf\Core\Modules\Setting\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;

class FrequentlyAskQuestionCategoryModel extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'faq_category';

    protected $fillable = ['name',];
}
