<?php

namespace Sanf\Core\Modules\OnBoarding\Models;

use NbsPhp\Core\Models\AbstractModel;

class OnBoardingModel extends AbstractModel
{
    protected $table = 'onboarding';

    protected $casts = [
        'image_file' => 'object'
    ];
}
