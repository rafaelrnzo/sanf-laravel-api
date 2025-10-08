<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Percepatan Pembayaran</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: Questrial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.5; color: #232227; background-color: #F2F2F2;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color: #F2F2F2; padding: 32px;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                    style="background-color: #F7FAFD; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">

                    <tr>
                        <td style="padding: 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="left" width="50%">
                                        <a href="{{ config('app.url') }}">
                                            <img src="{{ $leftLogo }}" alt="SANFIND Logo"
                                                style="max-width: 150px; height: auto;" />
                                        </a>
                                    </td>
                                    <td align="right" width="50%">
                                        <a href="{{ config('app.url') }}">
                                            <img src="{{ $rightLogo }}" alt="SANFIND Tagline"
                                                style="max-width: 150px; height: auto;" />
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 40px 40px;">

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td
                                        style="font-size: 20px; font-weight: 700; line-height: 1.7; margin-bottom: 20px; padding-bottom: 20px;">
                                        Kepada Tim {{ $bowheerName ?? 'NAMA BOWHEER' }}
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <p style="margin: 0 0 15px 0;">Selamat siang,</p>
                                        <p style="margin: 0;">Sebelumnya kami ucapkan terima kasih atas kesempatan
                                            {{ $initialSanf ?? 'SANF' }} dapat menjalin kerjasama dalam pembiayaan
                                            Invoice Financing Supplier dengan {{ $bowheerName ?? 'NAMA BOWHEER' }} dan
                                            {{ $clientName ?? 'NAMA CLIENT' }}, berikut terlampir dokumen invoice yang
                                            diajukan untuk {{ $clientName ?? 'NAMA CLIENT' }}. Mohon dibantu untuk
                                            verifikasi dan memberikan persetujuan terkait beberapa poin di bawah:</p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                            width="100%">
                                            <tr>
                                                <td width="25" style="vertical-align: top;">1.
                                                </td>
                                                <td style="vertical-align: top;">
                                                    Persetujuan atas Surat Permohonan Percepatan Pembayaran
                                                    ("Terlampir") dari {{ $clientName ?? 'NAMA CLIENT' }} selaku salah
                                                    satu Supplier {{ $bowheerName ?? 'NAMA BOWHEER' }}.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                            width="100%">
                                            <tr>
                                                <td width="25" style="vertical-align: top;">2.
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0 0 15px 0;">Persetujuan atas invoice-invoice
                                                        dengan nilai sebagaimana tercantum dalam tabel dibawah ini telah
                                                        disetujui dan diverifikasi kebenarannya:</p>

                                                    @if (!empty($tableData))
                                                        <table role="presentation" cellpadding="0" cellspacing="0"
                                                            border="1" width="100%"
                                                            style="border-collapse: collapse; border: 1px solid #777; margin: 15px 0;">
                                                            <thead>
                                                                <tr>
                                                                    @foreach ($tableHeaders as $header)
                                                                        <th
                                                                            style="background-color: #E9E9E9; padding: 8px 6px; text-align: center; border: 1px solid #777; font-size: 12px; font-weight: 400;">
                                                                            {{ $header['label'] }}
                                                                        </th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($tableData as $index => $row)
                                                                    <tr>
                                                                        @foreach ($tableHeaders as $header)
                                                                            <td
                                                                                style="padding: 6px; border: 1px solid #777; font-size: 11px; text-align: center; font-weight: 400;">
                                                                                {{ $row[$header['targetData']] ?? '?' }}
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="{{ count($tableHeaders) }}"
                                                                            style="text-align: center; color: #666666; padding: 15px; border: 1px solid #777; font-weight: 400; font-size: 12px;">
                                                                            Kesalahan dalam memuat data invoice.
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                            width="100%">
                                            <tr>
                                                <td width="25" style="vertical-align: top;">3.
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0 0 15px 0;">
                                                        {{ $sanfName ?? 'PT Surya Artha Nusantara Finance' }}
                                                        ({{ $initialSanf ?? 'SANF' }}) akan melakukan pembayaran
                                                        Invoice dipercepat (Detail Nomor 2) kepada
                                                        {{ $clientName ?? 'NAMA CLIENT' }} setelah dikurangi diskonto,
                                                        melalui transfer dengan rincian sebagai berikut:</p>

                                                    @if (!empty($allocationsTargetBank))
                                                        @foreach ($allocationsTargetBank as $bankSection)
                                                            <table role="presentation" cellpadding="0" cellspacing="0"
                                                                width="100%"
                                                                style="border-collapse: collapse; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000; margin-bottom: 15px;">
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: 400; color: #000000; padding: 8px 12px; background-color: #f9f9f9; width: 30%;">
                                                                        Nama Bank
                                                                    </td>
                                                                    <td
                                                                        style="color: #000000; font-weight: 500; padding: 8px 12px; background-color: #ffffff;">
                                                                        {{ $bankSection['title'] ?? 'NAMA BANK' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: 400; color: #000000; padding: 8px 12px; background-color: #f9f9f9; border-top: 1px solid #000000;">
                                                                        Nomor Rekening
                                                                    </td>
                                                                    <td
                                                                        style="color: #000000; font-weight: 500; padding: 8px 12px; background-color: #ffffff; border-top: 1px solid #000000;">
                                                                        {{ $bankSection['Nomor Rekening'] ?? 'NOMOR REKENING' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: 400; color: #000000; padding: 8px 12px; background-color: #f9f9f9; border-top: 1px solid #000000;">
                                                                        Atas Nama
                                                                    </td>
                                                                    <td
                                                                        style="color: #000000; font-weight: 500; padding: 8px 12px; background-color: #ffffff; border-top: 1px solid #000000;">
                                                                        {{ $bankSection['Atas Nama'] ?? 'ATAS NAMA' }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        @endforeach
                                                    @else
                                                        <div
                                                            style="border: 1px solid #000000; padding: 15px; background-color: #f9f9f9;">
                                                            Kesalahan dalam memuat data bank SANF.
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                            width="100%">
                                            <tr>
                                                <td width="25" style="vertical-align: top;">4.
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0 0 15px 0;">
                                                        {{ $bowheerName ?? 'NAMA BOWHEER' }} akan melakukan pembayaran
                                                        atas Invoice yang disetujui (Detail Nomor 2) dan akan dibayarkan
                                                        secara tepat waktu sesuai Tanggal Jatuh Tempo melalui Pembayaran
                                                        transfer kepada nomor Virtual Account dengan rincian sebagai
                                                        berikut:</p>

                                                    @if (!empty($clientTargetBank))
                                                        @foreach ($clientTargetBank as $bankSection)
                                                            <table role="presentation" cellpadding="0" cellspacing="0"
                                                                width="100%"
                                                                style="border-collapse: collapse; border-top: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000; margin-bottom: 15px;">
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: 400; color: #000000; padding: 8px 12px; background-color: #f9f9f9; width: 30%;">
                                                                        Nama Bank
                                                                    </td>
                                                                    <td
                                                                        style="color: #000000; font-weight: 500; padding: 8px 12px; background-color: #ffffff;">
                                                                        {{ $bankSection['title'] ?? 'NAMA BANK' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: 400; color: #000000; padding: 8px 12px; background-color: #f9f9f9; border-top: 1px solid #000000;">
                                                                        Nomor Rekening
                                                                    </td>
                                                                    <td
                                                                        style="color: #000000; font-weight: 500; padding: 8px 12px; background-color: #ffffff; border-top: 1px solid #000000;">
                                                                        {{ $bankSection['Nomor Rekening'] ?? 'NOMOR REKENING' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: 400; color: #000000; padding: 8px 12px; background-color: #f9f9f9; border-top: 1px solid #000000;">
                                                                        Atas Nama
                                                                    </td>
                                                                    <td
                                                                        style="color: #000000; font-weight: 500; padding: 8px 12px; background-color: #ffffff; border-top: 1px solid #000000;">
                                                                        {{ $bankSection['Atas Nama'] ?? 'ATAS NAMA' }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        @endforeach
                                                    @else
                                                        <div
                                                            style="border: 1px solid #000000; padding: 15px; background-color: #f9f9f9;">
                                                            Kesalahan dalam memuat data bank client.
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                width="100%">
                                <tr>
                                    <td style="padding-top: 30px;">
                                        <p style="margin: 0 0 15px 0;">Demikian permohonan kami atas konfirmasi
                                            beberapa persetujuan Invoice Financing
                                            {{ $bowheerName ?? 'NAMA BOWHEER' }}. Terima kasih atas bantuan dan
                                            waktunya.</p>

                                        <p style="margin: 0;">
                                            Best regards,<br>
                                            Customer Relation<br>
                                            {{ $sanfName ?? 'PT Surya Artha Nusantara Finance' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td
                            style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                width="100%">
                                <tr>
                                    <td style="text-align: center;">
                                        <p
                                            style="font-size: 12px; color: #666666; line-height: 1.5; margin: 0 0 10px 0;">
                                            Email ini dibuat secara otomatis mohon tidak membalas email ini, jika
                                            terdapat keluhan silahkan hubungi
                                            <a href="#"
                                                style="color: #3498db; text-decoration: none;">{{ $sanfInitial . ' Care' }}</a>.
                                        </p>
                                        <p
                                            style="font-size: 12px; color: #666666; line-height: 1.5; margin: 0 0 10px 0;">
                                            Jika anda merasa tidak membuat request tersebut mohon abaikan email ini atau
                                            anda dapat
                                            <a href="{{ $reportUrl }}"
                                                style="color: #3498db; text-decoration: none;">Laporkan email ini</a>.
                                        </p>
                                        <p style="font-size: 12px; color: #666666; line-height: 1.5; margin: 0;">
                                            &copy; {{ date('Y') }} {{ $sanfName }}. All rights reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <style>
        @media only screen and (max-width: 600px) {
            .mobile-padding {
                padding: 10px !important;
            }

            .mobile-font-size {
                font-size: 14px !important;
            }

            .mobile-table-font {
                font-size: 10px !important;
            }
        }
    </style>
</body>

</html>
