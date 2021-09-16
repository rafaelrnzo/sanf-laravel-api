<?php


namespace Sanf\Core\Modules\Commodity\Models;


use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\User\AuthModel;

class CommodityModel extends AbstractModel
{
    protected $table = 'commodity';

    protected $casts = [
        'image_file' => 'object',
        'location_metadata' => 'object',
        'modified_by' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(AuthModel::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(CommodityStatusModel::class, 'status_id');
    }
}
