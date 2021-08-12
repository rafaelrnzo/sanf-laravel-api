<?php


namespace Sanf\Api\Modules\User;


use NbsPhp\Core\Controllers\RestController;

class TitleController extends RestController
{
    public function getList()
    {
        $response = [
            [
                "name" => "CV",
                "id" => "1",
            ],
            [
                "name" => "DEPT",
                "id" => "4",
            ],
            [
                "name" => "UD",
                "id" => "20",
            ],
        ];

        return fractal(json_decode(json_encode($response)), new PositionTransformer());
    }
}
