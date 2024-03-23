<?php

namespace Tests\Assets\Resource\Resources;

use NbsPhp\ApiWrapper\Resource\ApiResource;
use NbsPhp\ApiWrapper\Resource\Contracts\All as AllContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Create as CreateContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Delete as DeleteContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Get as GetContract;
use NbsPhp\ApiWrapper\Resource\Contracts\Update as UpdateContract;
use NbsPhp\ApiWrapper\Resource\Operations\All;
use NbsPhp\ApiWrapper\Resource\Operations\Create;
use NbsPhp\ApiWrapper\Resource\Operations\Delete;
use NbsPhp\ApiWrapper\Resource\Operations\Get;
use NbsPhp\ApiWrapper\Resource\Operations\Update;

/**
 * Class JsonPlaceholderPost.
 * @property string id
 * @property string userId
 * @property string title
 * @property bool completed
 */
class JsonPlaceholderPost extends ApiResource implements AllContract, CreateContract, DeleteContract, GetContract, UpdateContract
{
    use All;
    use Create;
    use Delete;
    use Get;
    use Update;

    protected $allRoute = 'posts.all';
    protected $createRoute = 'posts.create';
    protected $deleteRoute = 'posts.delete';
    protected $getRoute = 'posts.get';
    protected $updateRoute = 'posts.update';
}
