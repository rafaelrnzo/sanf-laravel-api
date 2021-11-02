<?php


namespace Sanf\Core\Modules\Financing\Models;


use NbsPhp\Core\Models\AbstractModel;

class FinancingObjectModel extends AbstractModel
{
    protected $table = 'financing_object';

    const UPDATED_AT = null;

    protected $fillable = [
        'amount',
        'provider_name',
        'brand_id',
        'brand_name',
        'type_id',
        'type_name',
        'model_id',
        'model_name',
    ];
}
