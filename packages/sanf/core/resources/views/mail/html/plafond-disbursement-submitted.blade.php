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
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
        }
        .logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo {
            max-height: 50px;
            max-width: 150px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
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
        .table-container {
            overflow-x: auto;
            margin: 20px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .data-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 15px 12px;
            text-align: left;
            font-size: 14px;
        }
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .data-table tr:hover {
            background-color: #e3f2fd;
        }
        .bank-section {
            margin-bottom: 25px;
            padding: 20px;
            border: 2px solid #e3f2fd;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
        }
        .bank-title {
            font-weight: 700;
            font-size: 16px;
            color: #1565c0;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .bank-info {
            display: grid;
            gap: 10px;
        }
        .bank-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .bank-row:last-child {
            border-bottom: none;
        }
        .bank-label {
            font-weight: 600;
            color: #424242;
            flex: 1;
        }
        .bank-value {
            color: #1565c0;
            font-weight: 500;
            flex: 2;
            text-align: right;
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
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .logo-container {
                flex-direction: column;
                gap: 15px;
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
            <div class="logo-container">
                <img src="{{ $leftLogo }}" alt="SANF Logo" class="logo">
                <img src="{{ $rightLogo }}" alt="SANF Tagline" class="logo">
            </div>
            <h1>Pengajuan Percepatan Pembayaran</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Kepada Tim PT MENARA TERUS MAKMUR
            </div>

            <div class="section">
                <p>Selamat siang,</p>
                <p>Sebelumnya kami ucapkan terima kasih atas kerjasama SANF dengan menjadi partner dalam pencairan invoice Financing Supplier dengan PT MENARA TERUS MAKMUR dimasa yang akan datang. Dimana SANF meminta bantuan dalam tabel dibawah ini untuk disetujui dan diversifikasi secepatnya:</p>
            </div>

            <div class="section">
                <div class="section-title">1. Penyesuaian atas Surat Permohonan Percepatan Pembayaran</div>
                <div class="highlight">
                    <strong>Penyesuaian atas Surat Permohonan Percepatan Pembayaran (Terlampir) dari PT CGS INDONESIA sebagai salah satu Supplier PT MENARA TERUS MAKMUR sebelah kami telah menerima dokumen.</strong>
                </div>
            </div>

            <div class="section">
                <div class="section-title">2. Penyesuaian atas invoice-invoice dengan nilai sebagaimana tercantum dalam tabel dibawah ini telah disetujui dan diversifikasi secepatnya:</div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                @foreach($tableHeaders as $header)
                                    <th>{{ $header['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tableData as $row)
                                <tr>
                                    @foreach($tableHeaders as $header)
                                        <td>{{ $row[$header['targetData']] ?? '-' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section">
                <div class="section-title">3. PT Surya Artha Nusantara Finance (SANF) akan melakukan pembayaran invoice</div>
                <p>PT Surya Artha Nusantara Finance (SANF) akan melakukan pembayaran invoice diacapati (Detail Nomor 2) kepada rekening Virtual Account dengan rincian sebagai berikut:</p>
                
                @foreach($bankSections as $section)
                    <div class="bank-section">
                        <div class="bank-title">{{ $section['title'] }}</div>
                        <div class="bank-info">
                            @foreach($section['data'] as $key => $value)
                                <div class="bank-row">
                                    <span class="bank-label">{{ $key }}:</span>
                                    <span class="bank-value">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="section">
                <div class="section-title">4. Pembayaran oleh PT MENARA TERUS MAKMUR</div>
                <div class="highlight">
                    <strong>PT MENARA TERUS MAKMUR akan melakukan pembayaran atas Invoice yang disetujui (Detail Nomor 2) akan dibayarkan secara tepat waktu sesuai Tanggal Jatuh Tempo melalui Pembayaran transfer kepada nomor Virtual Account dengan rincian sebagai berikut:</strong>
                </div>
            </div>

            <div class="section">
                <p>Demikian permohonan kami atas konfirmasi beberapa persetujuan Invoice Financing PT CGS INDONESIA. Terima kasih atas bantuan dan waktunya.</p>
                
                <p><strong>Best regards,</strong><br>
                Customer Relation<br>
                PT. Surya Artha Nusantara Finance</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                <p>Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi <a href="#">SANF Care</a>.</p>
                <p>Jika anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat <a href="{{ $reportUrl }}">Laporkan email ini</a>.</p>
                <p>&copy; {{ date('Y') }} PT. Surya Artha Nusantara Finance. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>