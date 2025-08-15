<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @since CR2025
 */
class PdcHoldReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $order = 0;
        $now = Carbon::now();

        $data = [
            [
                'id' => 1,
                'name' => 'Sudah Bayar',
                'has_free_text' => false,
                'order' => ++$order,
                'created_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Tidak ada Dana',
                'has_free_text' => false,
                'order' => ++$order,
                'created_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Perubahan Metode Pembayaran',
                'has_free_text' => false,
                'order' => ++$order,
                'created_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Rekening ditutup',
                'has_free_text' => false,
                'order' => ++$order,
                'created_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Lainnya',
                'has_free_text' => true,
                'order' => ++$order,
                'created_at' => $now,
            ],
        ];

        DB::table('pdc_hold_reasons')->insertOrIgnore($data);
    }
}
