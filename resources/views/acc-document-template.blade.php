<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Percepatan Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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

        .signer-info {
            margin-top: 7em;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="document-no">
            <tr>
                <td>Nomor: <span>{{ $content['document_no'] }}</span></td>
                <td style="text-align: right;">Tanggal: {{ $content['document_date'] }}</td>
            </tr>
        </table>
        <p>Kepada Yth,</p>
        <p>Attn: {{ $content['bowheer']}}</p>
        <p>Subject: Permohonan Percepatan Pembayaran melalui program Vendor Financing</p>
        <p>Dengan hormat,</p>
        <p>Bersama ini kami bermaksud mengajukan Permohonan Percepatan Pembayaran atas tagihan-tagihan kami ke {{ $content['bowheer'] }} melalui program supplier financing yang difasilitasi oleh PT Surya Artha Nusantara Finance (SANF) sebesar Rp dengan detail perincian sebagaimana tercantum di LAMPIRAN 1.</p>
        <p>Dengan ini kami menyatakan tunduk dengan syarat dan ketentuan yang berlaku sesuai kontrak kerjasama antara {{ $content['bowheer'] }} dengan {{ $content['company'] }} yang masih berlaku sampai dengan tanggal surat ini. {{ $content['bowheer'] }} tidak akan melakukan gugatan dan/atau klaim dalam bentuk apapun yang bersifat akan merugikan {{ $content['company'] }} di kemudian hari atas permohonan percepatan pembayaran ini dan jenis transaksi lainnya yang berhubungan dan terkait dengan permohonan percepatan pembayaran ini. Segala akibat dan risiko baik dari aspek keuangan maupun non-keuangan yang timbul di kemudian hari atas permohonan percepatan pembayaran ini adalah menjadi tanggungan dan tanggung jawab kami.</p>
        <p>Demikian permohonan ini kami ajukan dan terima kasih atas perhatian dan kerjasama Bapak/Ibu.</p>
        <p>Hormat kami,</p>
    </div>
    <table class="signer">
        <tr>
            <td>
                <p>{{ $content['first_signer_company'] }}</p>
                <div class="signer-info">
                    <span>{{ $content['first_signer_name'] }}</span><br>
                    <span>{{ $content['first_signer_position'] }}</span>
                </div>
            </td>
            <td>
            </td>
            <td>
                <p>{{ $content['second_signer_company'] }}</p>
                <div class="signer-info">
                    <span>{{ $content['second_signer_name'] }}</span><br>
                    <span>{{ $content['second_signer_position'] }}</span>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
