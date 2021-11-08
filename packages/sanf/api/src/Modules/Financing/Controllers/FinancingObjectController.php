<?php

namespace Sanf\Api\Modules\Financing\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Financing\Transformers\BrandListTransformer;
use Sanf\Api\Modules\Financing\Transformers\ModelListTransformer;
use Sanf\Api\Modules\Financing\Transformers\TypeListTransformer;
use Sanf\Core\Modules\Financing\Services\GetBrandListService;
use Sanf\Core\Modules\Financing\Services\GetModelListService;
use Sanf\Core\Modules\Financing\Services\GetTypeListService;

class FinancingObjectController extends RestApiController
{
    public function getBrands(GetBrandListService $service)
    {
        $result = $service->execute();

        return fractal($result->data, BrandListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getTypes($brand_id, GetTypeListService $service)
    {
        $dto = (object)[
            'brand_id' => $brand_id
        ];

        $result = $service->execute($dto);

        return fractal($result->data, TypeListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getModels($brand_id, $type_id, GetModelListService $service)
    {
        $dto = (object)[
            'brand_id' => $brand_id,
            'type_id' => $type_id,
        ];
        $result = $service->execute($dto);

        return fractal($result->data, ModelListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getTNC()
    {
        $data = [
            (object)[
                'name' => 'is_confirm_first',
                'description' => 'Seluruh data, informasi, dan/atau dokumen yang kami berikan adalah benar dan merupakan dokumen terbaru yang masih berlaku. *'
            ],
            (object)[
                'name' => 'is_confirm_second',
                'description' => 'Seluruh data dan/atau informasi pribadi yang diberikan dapat dipergunakan dan diinformasikan kepada karyawan, vendor, afiliasi, instansi yang berwenang dan/atau pihak-pihak lainnya yang terkait dengan SANF dan/atau afiliasinya sesuai dengan peraturan perundang-undangan yang berlaku. *'
            ],
            (object)[
                'name' => 'is_confirm_third',
                'description' => 'Seluruh data dan/atau informasi pribadi yang diberikan dapat dipergunakan dan diinformasikan kepada karyawan, vendor, afiliasi, instansi yang berwenang dan/atau pihak-pihak lainnya yang terkait dengan SANF dan/atau afiliasinya sesuai dengan peraturan perundang-undangan yang berlaku. *'
            ],
            (object)[
                'name' => 'is_confirm_fourth',
                'description' => 'Melalui formulir ini, kami memberikan data dan/atau informasi di atas kepada SANF dan untuk itu kami memberi kuasa kepada SANF untuk melakukan tindakan - tindakan lebih lanjut sehubungan dengan hal tersebut serta kami menyatakan bahwa kami akan mematuhi seluruh ketentuan peraturan perundang-undangan yang berlaku. *'
            ],
            (object)[
                'name' => 'is_receive_offer',
                'description' => 'Kami bersedia untuk mendapatkan penawaran dan/atau informasi produk dari SANF melalui alamat, telepon, faximile, dan/atau email yang kami cantumkan bersama dengan Formulir Data Customer ini.'
            ]
        ];

        return $this->responseOk('Success', [
            'tnc' => $data
        ]);
    }
}