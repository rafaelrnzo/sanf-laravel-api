<?php

namespace NbsPhp\Core\Models;

use NbsPhp\Core\Models\AuthModel as BaseAuthModel;
use NbsPhp\Core\Traits\RbacTrait;

class AuthRbacModel extends BaseAuthModel
{
    use RbacTrait;
}
