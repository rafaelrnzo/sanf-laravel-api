<?php

namespace Sanf\Core\Modules\Faq\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;

class FaqModel extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'faq';

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'is_popular',
        'order',
    ];

    public function faqCategory()
    {
        return $this->belongsTo(FaqCategoryModel::class, 'category_id');
    }
}
