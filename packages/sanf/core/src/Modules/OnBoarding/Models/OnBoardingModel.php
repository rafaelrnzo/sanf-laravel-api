<?php

namespace Sanf\Core\Modules\OnBoarding\Models;

use NbsPhp\Core\Models\AbstractModel;

class OnBoardingModel extends AbstractModel
{
    protected $table = 'onboarding';

    protected $fillable = [
        'title',
        'description',
        'image_file',
    ];

    protected $casts = [
        'image_file' => 'object'
    ];
}
