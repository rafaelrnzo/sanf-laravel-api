<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Percepatan Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .header {
            padding-left: 32px;
            padding-right: 32px;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .content {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: 600;
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
        }

        .signature-section {
            margin-top: 30px;
            line-height: 1.6;
        }

        .numbered-list {
            counter-reset: list-counter;
            list-style: none;
            padding: 0;
        }

        .numbered-list li {
            counter-increment: list-counter;
            margin-bottom: 20px;
            position: relative;
            padding-left: 25px;
        }

        .numbered-list li:before {
            content: counter(list-counter) ".";
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .table-container {
            margin: 15px 0;
            border: 1px solid #000;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            font-size: 11px;
        }

        .data-table th {
            background-color: #f0f0f0;
            color: #000;
            font-weight: bold;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #000;
            font-size: 11px;
        }

        .data-table td {
            padding: 6px;
            border: 1px solid #000;
            font-size: 11px;
            text-align: center;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .bank-section {
            margin-bottom: 15px;
            border: 1px solid #000;
            background-color: #fff;
        }

        .bank-title {
            font-weight: bold;
            font-size: 12px;
            color: #000;
            background-color: #f0f0f0;
            padding: 8px 12px;
            border-bottom: 1px solid #000;
            margin: 0;
        }

        .bank-info {
            padding: 0;
        }

        .bank-row {
            display: flex;
            border-bottom: 1px solid #000;
            padding: 0;
        }

        .bank-row:last-child {
            border-bottom: none;
        }

        .bank-label {
            font-weight: normal;
            color: #000;
            flex: 1;
            padding: 8px 12px;
            border-right: 1px solid #000;
            background-color: #f9f9f9;
        }

        .bank-value {
            color: #000;
            font-weight: normal;
            flex: 2;
            padding: 8px 12px;
            background-color: #fff;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .footer-text {
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }

        .footer-text a {
            color: #3498db;
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
            border-radius: 4px;
        }

        .info-section {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .info-title {
            font-weight: 600;
            color: #1565c0;
            margin-bottom: 10px;
        }

        .info-content {
            color: #424242;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 20px;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 8px 6px;
            }

            .bank-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .bank-value {
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="left" width="50%">
                        <a href="{{ config('app.url') }}">
                            <img src="{{ $leftLogo }}" alt="SANFIND Logo" class="tb-50" />
                        </a>
                    </td>
                    <td align="right" width="50%">
                        <a href="{{ config('app.url') }}">
                            <img src="{{ $rightLogo }}" alt="SANFIND Tagline" class="tb-50" />
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Kepada Tim {{ $companyName ?? 'PT MENARA TERUS MAKMUR' }}
            </div>

            <div class="section">
                <p>Selamat siang,</p>
                <p>Sebelumnya kami ucapkan terima kasih atas kesempatan SANF dapat menjalin kerjasama dalam pembiayaan
                    Invoice Financing Supplier dengan {{ $companyName ?? 'PT MENARA TERUS MAKMUR' }} dan PT CGS
                    INDONESIA, berikut terlampir dokumen invoice yang diajukan untuk PT CGS INDONESIA. Mohon dibantu
                    untuk verifikasi dan memberikan persetujuan terkait beberapa poin di bawah:</p>
            </div>

            <ol class="numbered-list">
                <li>
                    <strong>Persetujuan atas Surat Permohonan Percepatan Pembayaran ("Terlampir") dari PT CGS INDONESIA
                        selaku salah satu Supplier PT {{ $companyName ?? 'MENARA TERUS MAKMUR' }}.</strong>
                </li>

                <li>
                    <strong>Persetujuan atas invoice-invoice dengan nilai sebagaimana tercantum dalam tabel dibawah ini
                        telah disetujui dan diverifikasi kebenarannya:</strong>

                    @if (!empty($tableData))
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        @foreach ($tableHeaders as $header)
                                            <th>{{ $header['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tableData as $row)
                                        <tr>
                                            @foreach ($tableHeaders as $header)
                                                <td>{{ $row[$header['targetData']] ?? '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($tableHeaders) }}"
                                                style="text-align: center; color: #666;">
                                                Tidak ada data invoice tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </li>

                <li>
                    <strong>PT Surya Artha Nusantara Finance (SANF) akan melakukan pembayaran Invoice dipercepat (Detail
                        Nomor 2) kepada PT CGS INDONESIA setelah dikurangi diskonto, melalui transfer dengan rincian
                        sebagai berikut:</strong>

                    @if (!empty($bankSections))
                        @foreach ($bankSections as $index => $section)
                            @if ($index === 0)
                                <div class="bank-section">
                                    <div class="bank-title">{{ $section['title'] ?? 'BANK PERMATA' }}</div>
                                    <div class="bank-info">
                                        <div class="bank-row">
                                            <div class="bank-label">Nomor Rekening</div>
                                            <div class="bank-value">
                                                {{ $section['data']['Nomor Rekening'] ?? '00701570456' }}</div>
                                        </div>
                                        <div class="bank-row">
                                            <div class="bank-label">Atas Nama</div>
                                            <div class="bank-value">
                                                {{ $section['data']['Atas Nama'] ?? 'PT CGS INDONESIA' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="bank-section">
                            <div class="bank-title">BANK PERMATA</div>
                            <div class="bank-info">
                                <div class="bank-row">
                                    <div class="bank-label">Nomor Rekening</div>
                                    <div class="bank-value">00701570456</div>
                                </div>
                                <div class="bank-row">
                                    <div class="bank-label">Atas Nama</div>
                                    <div class="bank-value">PT CGS INDONESIA</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </li>

                <li>
                    <strong>PT {{ $companyName ?? 'MENARA TERUS MAKMUR' }} akan melakukan pembayaran atas Invoice yang
                        disetujui (Detail Nomor 2) dan akan dibayarkan secara tepat waktu sesuai Tanggal Jatuh Tempo
                        melalui Pembayaran transfer kepada nomor Virtual Account dengan rincian sebagai
                        berikut:</strong>

                    @if (!empty($bankSections))
                        @foreach ($bankSections as $index => $section)
                            @if ($index > 0)
                                <div class="bank-section">
                                    <div class="bank-title">{{ $section['title'] }}</div>
                                    <div class="bank-info">
                                        @foreach ($section['data'] as $key => $value)
                                            <div class="bank-row">
                                                <div class="bank-label">{{ $key }}</div>
                                                <div class="bank-value">{{ $value }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="bank-section">
                            <div class="bank-title">BANK PERMATA</div>
                            <div class="bank-info">
                                <div class="bank-row">
                                    <div class="bank-label">Nomor Rekening/Virtual Account</div>
                                    <div class="bank-value">8876200000447201</div>
                                </div>
                                <div class="bank-row">
                                    <div class="bank-label">Atas Nama</div>
                                    <div class="bank-value">PT CGS INDONESIA QQ PT Surya Artha Nusantara Finance</div>
                                </div>
                            </div>
                        </div>

                        <div class="bank-section">
                            <div class="bank-title">BANK MANDIRI</div>
                            <div class="bank-info">
                                <div class="bank-row">
                                    <div class="bank-label">Nomor Rekening/Virtual Account</div>
                                    <div class="bank-value">8890932600000472</div>
                                </div>
                                <div class="bank-row">
                                    <div class="bank-label">Atas Nama</div>
                                    <div class="bank-value">PT CGS INDONESIA QQ PT Surya Artha Nusantara Finance</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </li>
            </ol>

            <div class="signature-section">
                <p>Demikian permohonan kami atas konfirmasi beberapa persetujuan Invoice Financing PT CGS INDONESIA.
                    Terima kasih atas bantuan dan waktunya.</p>

                <p><strong>Best regards,</strong><br>
                    Customer Relation<br>
                    PT. Surya Artha Nusantara Finance</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                <p>Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan
                    hubungi <a href="#">SANF Care</a>.</p>
                <p>Jika anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat <a
                        href="{{ $reportUrl }}">Laporkan email ini</a>.</p>
                <p>&copy; {{ date('Y') }} PT. Surya Artha Nusantara Finance. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>

</html>
