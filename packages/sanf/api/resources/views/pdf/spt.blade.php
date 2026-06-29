<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Transfer</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333333;
            line-height: 1.6;
        }
        @page {
            margin: 1cm 2cm 2cm 2cm;
        }
        .page-break {
            page-break-after: always;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header .company-address {
            font-size: 11px;
            color: #666666;
            margin-top: 5px;
        }
        .doc-meta {
            width: 100%;
            margin-bottom: 25px;
        }
        .doc-meta td {
            vertical-align: top;
        }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .content {
            margin-bottom: 25px;
            text-align: justify;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .table-info td {
            padding: 6px 10px;
            vertical-align: top;
        }
        .table-info td.label {
            width: 30%;
            font-weight: bold;
        }
        .table-info td.separator {
            width: 3%;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        .table-data th {
            border-bottom: 1px solid #dddddd;
            padding: 6px 8px;
            font-weight: bold;
            background-color: transparent;
            text-align: left;
        }
        .table-data td {
            border-bottom: 1px solid #eeeeee;
            padding: 6px 8px;
            text-align: left;
        }
        .table-data td.number, .table-data th.number {
            text-align: right;
        }
        .table-data td.center, .table-data th.center {
            text-align: center;
        }
        .footer-sig {
            width: 100%;
            margin-top: 50px;
        }
        .footer-sig td {
            width: 50%;
            vertical-align: top;
        }
        .sig-space {
            height: 80px;
        }
        .page-num {
            text-align: right;
            font-size: 10px;
            color: #999999;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">{{ $customer['identity_name'] ?? 'NAMA PERUSAHAAN CUSTOMER' }}</div>
        <div class="company-address">
            {{ $customer['address'] ?? '' }}<br>
            Telp: {{ $customer['phone'] ?? '-' }} | Email: {{ $customer['email'] ?? '' }}
        </div>
    </div>

    <table class="doc-meta">
        <tr>
            <td style="width: 60%;">
                <strong>Nomor Surat:</strong> {{ $letter_number }}<br>
                <strong>Tanggal:</strong> {{ $letter_date->translatedFormat('d F Y') }}
            </td>
            <td style="width: 40%; text-align: right;">
                Kepada Yth.<br>
                <strong>PT Surya Artha Nusantara Finance (SANF)</strong><br>
                Jakarta
            </td>
        </tr>
    </table>

    <div class="doc-title">Surat Permohonan Realisasi Pencairan (SPT)</div>

    <div class="content">
        Dengan hormat,<br><br>
        Sehubungan dengan fasilitas <em>Standby Financing</em> (PO Financing) yang telah disetujui, bersama ini kami mengajukan permohonan realisasi pencairan pembiayaan dengan rincian sebagai berikut:
    </div>

    <table class="table-info">
        <tr>
            <td class="label">Nomor Plafond</td>
            <td class="separator">:</td>
            <td>{{ $plafond_number }}</td>
        </tr>
        <tr>
            <td class="label">Total Nilai PO/Invoice</td>
            <td class="separator">:</td>
            <td>Rp{{ number_format($total_po_amount, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Nominal Pencairan</td>
            <td class="separator">:</td>
            <td><strong>Rp{{ number_format($total_disbursement, 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Rekening Tujuan Transfer</td>
            <td class="separator">:</td>
            <td>
                {{ $bank_account['provider'] ?? '' }} - No. Rekening: {{ $bank_account['account_number'] ?? '' }}<br>
                Atas Nama: {{ $bank_account['owner'] ?? '' }}
            </td>
        </tr>
    </table>

    <div class="content">
        Rincian dokumen pendukung atas permohonan realisasi pencairan ini terlampir pada halaman kedua dari surat ini. Kami menyatakan bahwa seluruh data dan dokumen yang kami lampirkan adalah benar dan valid.
    </div>

    <div class="content">
        Demikian permohonan ini kami sampaikan. Atas perhatian dan kerjasama Bapak/Ibu, kami ucapkan terima kasih.
    </div>

    <div class="page-num">Halaman 1 dari 2</div>

    <div class="page-break"></div>

    <div style="font-size: 14px; font-weight: bold; margin-bottom: 20px; text-decoration: underline; text-transform: uppercase; border-bottom: 2px solid #000000; padding-bottom: 10px;">
        Lampiran: Rincian Dokumen Pendukung
    </div>

    <div class="content" style="margin-bottom: 15px;">
        <strong>Nomor Surat:</strong> {{ $letter_number }}<br>
        <strong>Tanggal:</strong> {{ $letter_date->translatedFormat('d F Y') }}<br>
        <strong>Nomor Plafond:</strong> {{ $plafond_number }}
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th class="center" style="width: 8%;">No</th>
                <th style="width: 52%;">Nomor PO / Invoice</th>
                <th class="center" style="width: 20%;">Tanggal Dokumen</th>
                <th class="number" style="width: 20%;">Nilai Dokumen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice_list as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item['nomor'] }}</td>
                    <td class="center">{{ !empty($item['tanggal']) ? \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d/m/Y') : '-' }}</td>
                    <td class="number">Rp{{ number_format($item['amount'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="3" class="center">TOTAL NILAI DOKUMEN</td>
                <td class="number">Rp{{ number_format($total_po_amount, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-sig" style="margin-top: 50px;">
        <tr>
            <td>&nbsp;</td>
            <td style="text-align: center;">
                Hormat Kami,<br>
                <strong>{{ $customer['identity_name'] ?? 'NAMA PERUSAHAAN CUSTOMER' }}</strong>
                <div class="sig-space"></div>
                <u><strong>{{ $signer_name }}</strong></u><br>
                <span>{{ $signer_role }}</span>
            </td>
        </tr>
    </table>

    <div class="page-num" style="margin-top: 30px;">Halaman 2 dari 2</div>

</body>
</html>
