<?php


namespace Sanf\Api\Modules\User;


use NbsPhp\Core\Controllers\RestController;

class PositionController extends RestController
{
    public function getList()
    {
        $response = [
            [
                "name" => "ANGGOTA DPRD",
                "id" => "033"
            ],
            [
                "name" => "BENDAHARA",
                "id" => "051"
            ],
            [
                "name" => "BENDAHARA I/II",
                "id" => "052"
            ]
        ];

        return fractal(json_decode(json_encode($response)), new PositionTransformer());
    }
}
