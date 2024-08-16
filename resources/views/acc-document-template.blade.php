<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Percepatan Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            display: flex;
            justify-content: center;
            margin-top: 50px;
        }

        p {
            text-align: justify;
        }

        .container {
            line-height: 1.6;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
        }

        table td {
            vertical-align: top;
        }

        .document-no span {
            text-decoration: underline;
        }

        .signer td {
            width: 33.33%;
            height: 150px;
        }

        .first-signer-info {
            margin-top: 2.8em;
        }

        .second-signer-info {
            margin-top: 7em;
        }

        .page-break {
            page-break-after: always;
        }

        section.attachment {
            text-align: center;
        }

        section.attachment div span {
            font-weight: bold;
        }

        table.invoice {
            border-collapse: collapse;
            width: 100%;
            margin-top: 40px;
            font-size: 11px;
        }

        table.invoice td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        table.invoice td {
            vertical-align: top;
        }

        table.invoice td.index {
            width: 3%;
            text-align: center;
        }

        table.invoice td.invoice-no {
            max-width: 15%;
        }

        table.invoice .last {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <section>
            <table class="document-no">
                <tr>
                    <td>Nomor: <span>{{ $content['document_no'] }}</span></td>
                    <td style="text-align: right;">Tanggal: {{ $content['document_date'] }}</td>
                </tr>
            </table>
            <p>
                <span>Kepada Yth,<span><br /><span>{{ $content['customer'] }}<span><br /><span>{{ $content['customer_address'] }}</span>
            </p>
            <p>Attn: {{ $content['client']}}</p>
            <p>Subject: Permohonan Percepatan Pembayaran melalui program Vendor Financing</p>
            <p>Dengan hormat,</p>
            <p>Bersama ini kami bermaksud mengajukan Permohonan Percepatan Pembayaran atas tagihan-tagihan kami ke {{ $content['customer'] }} melalui program supplier financing yang difasilitasi oleh PT Surya Artha Nusantara Finance (SANF) sebesar {{ $content['total_amount' ]}} dengan detail perincian sebagaimana tercantum di LAMPIRAN 1.</p>
            <p>Dengan ini kami menyatakan tunduk dengan syarat dan ketentuan yang berlaku sesui kontrak kerjasama antara {{ $content['client'] }} dengan {{ $content['customer'] }} yang masih berlaku sampai dengan tanggal surat ini. {{ $content['client'] }} tidak akan melakukan gugatan dan/atau klaim dalam bentuk apapun yang bersifat akan merugikan {{ $content['customer'] }} di kemudian hari atas permohonan percepatan pembayaran ini dan jenis transaksi lainnya yang berhubungan dan terkait dengan permohonan percepatan pembayaran ini. Segala akibat dan risiko baik dari aspek keuangan maupun non-keuangan yang timbul di kemudian hari atas permohonan percepatan pembayaran ini adalah menjadi tanggungan dan tanggung jawab kami.</p>
            <p>Demikian permohonan ini kami ajukan dan terima kasih atas perhatian dan kerjasama Bapak/Ibu.</p>
            <p>Hormat kami,</p>
            <table class="signer">
                <tr>
                    <td>
                        <p>{{ $content['first_signer_company'] }}</p>
                        <p>[Dokumen ini dihasilkan secara digital dan tidak memerlukan tanda tangan basah]</p>
                        <div class="first-signer-info">
                            <span>{{ $content['first_signer_name'] }}</span><br>
                            <span>{{ $content['first_signer_position'] }}</span>
                        </div>
                    </td>
                    <td>
                    </td>
                    <td>
                        <p>{{ $content['second_signer_company'] }}</p>
                        <div class="second-signer-info">
                            <span>{{ $content['second_signer_name'] }}</span><br>
                            <span>{{ $content['second_signer_position'] }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
        <div class="page-break"></div>
        <section class="attachment">
            <div><span>Lampiran 1</span></div>
            <table class="invoice">
                <tr>
                    <th>No</th>
                    <th>No Invoice</th>
                    <th>Tanggal Invoice</th>
                    <th>Sub Total</th>
                    <th>Ppn</th>
                    <th>Pph</th>
                    <th>Backharge</th>
                    <th>Total</th>
                </tr>
                @foreach ($content['invoices'] as $invoice)
                <tr>
                    <td class="index">{{ $invoice['index'] }}</td>
                    <td class="invoice-no">{{ $invoice['no'] }}</td>
                    <td>{{ $invoice['date'] }}</td>
                    <td>{{ $invoice['amount'] }}</td>
                    <td>{{ $invoice['vat_amount'] }}</td>
                    <td>{{ $invoice['tax_amount'] }}</td>
                    <td>{{ $invoice['backharge_amount']}}</td>
                    <td>{{ $invoice['total_amount'] }}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="last" colspan="7">Total</td>
                    <td>{{ $content['total_amount']}} </td>
                </tr>
            </table>
        </section>
    </div>
</body>

</html>
