<?php

namespace Sanf\Api\Modules\Insurance;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Mail\MailLayout2Columns;

class InsuranceController extends RestApiController
{
    public function sendEmailUser()
    {
        $data = [
            'Tanggal Pengajuan' => Carbon::parse('2021-12-10')->formatLocalized('%A, %d %B %Y'),
            'Serial Number' => 'MHYGBHJ81NGHJ1150234',
            'No Polisi' => '02345678900',
            'Data Unit' => 'Hitachi Excavator ZX350H-5G21',
            'Tahun Kendaraan' => '2021',
            'Lokasi Pertangguhan' => 'Jakarta Selatan',
            'Tanggal Kejadian' => Carbon::parse('2021-10-09')->formatLocalized('%d/%m/%Y'),
            'Keterangan' => '-',
        ];

        $user = [
            'name' => 'John Doe',
            'email' => 'johndoe@mail.com'
        ];

        $insurance = (new MailLayout2Columns)
            ->subject('Pengajuan Klaim Asuransi')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__("Halo $user[name]!"))
            ->line(__('Berikut kami lampirkan data untuk pengajuan klaim asuransi Anda untuk'
            ))
            ->writeContent($data)
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
                ['joinToIndex' => 2, 'html' => '<p style="color: #232227; font-size: 14px;"><strong>Detail Klaim Asuransi</strong></p>'],
                ['joinToIndex' => 7, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
            ])
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('Sanf Customer Service'), '#']
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), '#']
            );

        $insurance->attach(public_path('assets/news-1.png'));

        return Mail::to($user['email'])->send($insurance);
    }

    public function sendEmailAdmin()
    {
        $data = [
            'Tanggal Pengajuan' => Carbon::parse('2021-12-10')->formatLocalized('%A, %d %B %Y'),
            'Serial Number' => 'MHYGBHJ81NGHJ1150234',
            'No Polisi' => '02345678900',
            'Data Unit' => 'Hitachi Excavator ZX350H-5G21',
            'Tahun Kendaraan' => '2021',
            'Lokasi Pertangguhan' => 'Jakarta Selatan',
            'Tanggal Kejadian' => Carbon::parse('2021-10-09')->formatLocalized('%d/%m/%Y'),
            'Keterangan' => '-',
        ];

        $user = [
            'name' => 'John Doe',
            'email' => 'johndoe@mail.com'
        ];

        $insurance = (new MailLayout2Columns)
            ->subject('Pengajuan Klaim Asuransi '. $user['name'])
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__("Halo Admin SANF!"))
            ->line(__('Pengguna atas nama <strong>“'. $user['name']. '”</strong> telah mengajukan klaim asuransi, berikut kami lampirkan detailnya'
            ))
            ->writeContent($data)
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
                ['joinToIndex' => 2, 'html' => '<p style="color: #232227; font-size: 14px;"><strong>Detail Klaim Asuransi</strong></p>'],
                ['joinToIndex' => 7, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
            ])
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('Sanf Customer Service'), '#']
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), '#']
            );

        $insurance->attach(public_path('assets/news-1.png'));

        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));

        return Mail::to($recipients)->send($insurance);
    }

}
