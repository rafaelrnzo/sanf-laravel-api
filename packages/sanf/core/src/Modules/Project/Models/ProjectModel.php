<?php

namespace Sanf\Core\Modules\Project\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\User\AuthModel;

class ProjectModel extends AbstractModel
{
    protected $table = 'project';

    protected $dates = ['submission_limit_at'];

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
        return $this->belongsTo(ProjectStatusModel::class, 'status_id');
    }
}
