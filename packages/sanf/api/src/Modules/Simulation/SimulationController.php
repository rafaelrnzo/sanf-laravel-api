<?php

namespace Sanf\Api\Modules\Simulation;

use NbsPhp\Core\Controllers\RestApiController;

class SimulationController extends RestApiController
{


     public function process()
     {
          $result = [
               'name'    => 'John Doe',
               'email'   => 'johndoe@mail.com',
               'data'    => [
                    'jenis_pembiayaan'       => 'Factoring with resource',
                    'total_pembiayaan'       => 'Rp. ' . number_format(10000000000, 0, ',', '.'),  // params-1, should be value from DB
                    'uang_muka'              => 'Rp. ' . number_format(5000000000, 0, ',', '.'), // // params-1, should be value from DB
                    'persen_dp'              => strval(50) . '%', // should be value from DB
                    'tenor'                  => '12 Bulan', // assuming return this string, if not then iterate string with month
                    'angsuran_perbulan'      => 'Rp. ' . number_format(416666666.666, 3, ',', '.'), // should be value from DB
                    'suku_bunga'             => strval(10) . '%' // should be value from DB
               ]
          ];

          $recipients = explode(',', config('sanf-mobile.mail_to_admin'));

          dispatch(new SendSimulationJob($result, $recipients));
     }
}
