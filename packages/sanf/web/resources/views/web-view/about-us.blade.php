<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.rawgit.com/mfd/09b70eb47474836f25a21660282ce0fd/raw/e06a670afcb2b861ed2ac4a1ef752d062ef6b46b/Gilroy.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@xz/fonts@1/serve/hk-grotesk.min.css">
</head>

<body>
  <style>
    :root {
      --spacer: 16px;
    }

    .sf-wrapper {
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      overflow: hidden;
    }

    .sf-container {
      width: 100%;
      padding-right: 15px;
      padding-left: 15px;
      margin-right: auto;
      margin-left: auto;
    }

    .sf-logo-header {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding-top: 30px;
      padding-bottom: 50px;
    }

    .sf-logo-header>img {
      width: 45%;
      object-fit: cover;
    }

    .sf-content {
      display: flex;
      flex-direction: column;
      padding-bottom: 10%;
    }

    .sf-title {
      font-family: 'Gilroy';
      font-style: normal;
      font-weight: bold;
      font-size: 16px;
      line-height: 24px;
      color: #232227;
      margin: 0;
    }

    .sf-text {
      font-family: 'Gilroy';
      font-style: normal;
      font-weight: normal;
      font-size: 14px;
      line-height: 150%;
      color: #999BAC;
      margin-bottom: 0;
      margin-top: 8px;
    }

    .sf-text-version {
      font-family: 'HK Grotesk';
      font-style: normal;
      font-weight: normal;
      font-size: 12px;
      line-height: 20px;
      color: #232227;
    }

    .sf-social-media {
      display: flex;
      justify-content: start;
      align-items: center;
    }

    .sf-social-media>a {
      flex: .1 .1 auto;
      cursor: pointer;
    }

    .sf-ojk {
      width: 108px;
      height: 54px;
      padding: 5px;
    }

    .sf-ojk > img {
      width: 100%;
      object-fit: cover;
    }

    .sf-satu-indonesia {
      width: 160px;
      height: 80px;
      margin: 0 auto;
    }

    .sf-satu-indonesia > img {
      width: 100%;
      object-fit: cover;
    }

    .sf-list-style {
      font-family: 'Gilroy';
      font-weight: 400;
      font-style: normal;
      font-size: 14px;
      line-height: 21px;
      color: #999BAC;
      padding-inline-start: 15px;
    }

    .sf-list-style>li {
      padding-left: 10px;
    }

    .mb-0 {
      margin-bottom: 0px !important;
    }

    .mt-0 {
      margin-top: 0px !important;
    }

    .mt-1 {
      margin-top: calc(var(--spacer) * .25) !important;
    }

    .mt-2 {
      margin-top: calc(var(--spacer) * .5) !important;
    }

    .mt-3 {
      margin-top: var(--spacer);
    }

    .mt-4 {
      margin-top: calc(var(--spacer) * 1.5) !important;
    }

    .mt-5 {
      margin-top: calc(var(--spacer) * 3) !important;
    }

    /* Phone (iphone x) Landscape */
    @media only screen 
      and (min-device-width: 375px) 
      and (max-device-width: 812px) 
      and (-webkit-min-device-pixel-ratio: 3)
      and (orientation: landscape) { 
        .sf-social-media>a {
          flex: .05 .05 auto;
          cursor: pointer;
        }

    }
    /* END Phone  */

    /* IPAD */
    @media only screen 
      and (min-device-width: 768px) 
      and (max-device-width: 1024px) 
      and (-webkit-min-device-pixel-ratio: 1) {
        .sf-social-media>a {
          flex: .06 .06 auto;
        }

        .sf-social-media>a>img {
          width: 64px;
          height: 64px;
        }
    }
    /* END IPAD */
    
    /* IPAD PRO */
    @media only screen 
      and (min-width: 1024px) 
      and (max-height: 1366px) 
      and (-webkit-min-device-pixel-ratio: 1.5) {
        .sf-social-media>a {
          flex: .05 .05 auto;
        }

        .sf-social-media>a>img {
          width: 64px;
          height: 64px;
        }
    }
    /* END IPAD PRO */
  </style>

  <div class="sf-wrapper">
    <div class="sf-container">
      <div class="sf-content">
        <div class="sf-logo-header">
          <img src="{{ asset('assets/png/sanf-logo-blue.png') }}" alt="SANF" />
        </div>
        <p class="sf-text mt-4">
          PT Surya Artha Nusantara Finance (SANF) menyediakan layanan dan produk keuangan yang
          menyeluruh dalam layanan pembiayaan investasi dan pembiayaan modal kerja yang berorientasi
          kepada pemenuhan kebutuhan konsumen.
        </p>
        <div class="mt-4">
          <p class="sf-title">Visi Perseroan</p>
          <P class="sf-text">
            Menjadi perusahaan pembiayaan pilihan dengan menyediakan solusi keuangan terbaik
            bagi mitra bisnis di Indonesia
          </P>
        </div>
        <div class="mt-4">
          <p class="sf-title">Misi Perseroan</p>
          <p class="sf-text">
            Menyediakan produk dan layanan keuangan terbaik melalui kerjasama bisnis yang
            berkesinambungan untuk kesejahteraan bersama
          </p>
        </div>
        <div class="mt-4 mb-0">
          <p class="sf-title">Kegiatan Usaha Perseroan</p>
          <p class="sf-text">
            Berdasarkan Akta tertanggal 29-05-2015 (duapuluh sembilan Mei dua ribu lima belas) nomor 78, yang dibuat
            dihadapan KUMALA TJAHJANI WIDODO, Sarjana Hukum, Magister Hukum, Magister Kenotariatan, Notaris di Jakarta,
            yang telah disetujui oleh Menteri Hukum dan Hak Asasi Manusia Republik Indonesia melalui Surat Keputusan
            tertanggal 12-06-2015 (duabelas Juni duaribu limabelas) nomor: AHU-0937214.AH.01.02.TAHUN 2015 dan
            pemberitahuan perubahan data Perseroan telah diterima dan dicatat di dalam Sistem Administrasi Badan Hukum
            oleh Menteri Hukum dan Hak Asasi Manusia Republik Indonesia berdasarkan Surat tertanggal
            12-06-2015 (duabelas Juni duaribu limabelas) nomor AHU-AH.01.03-0940743, Perseroan dapat melaksanakan
            kegiatan usaha sebagai berikut:
          <ol type="a" class="sf-list-style">
            <li>
              Menjalankan usaha-usaha di bidang Pembiayaan Investasi, yaitu pembiayaan untuk pengadaan
              barang-barang modal beserta jasa yang diperlukan untuk aktivitas usaha/investasi, rehabilitasi,
              modernisasi, ekspansi atau relokasi tempat usaha/investasi yang diberikan kepada debitur dalam
              jangka waktu lebih dari 2 (dua) tahun, yang wajib dilakukan dengan cara:

              <ol type="1" class="sf-list-style">
                <li>Jual dan Sewa-Balik (Sale and Lease Back).</li>
                <li>Anjak Piutang Dengan Pemberian Jaminan Dari Penjual Piutang (Factoring With Recourse).</li>
                <li>Anjak Piutang Tanpa Pemberian Jaminan Dari Penjual Piutang (Factoring Without Recourse).</li>
                <li>Fasilitas Modal Usaha; dan/atau.</li>
                <li>Pembiayaan lain setelah terlebih dahulu mendapatkan persetujuan dari Otoritas Jasa Keuangan.</li>
              </ol>

            </li>
            <li class="mt-3">
              Menjalankan usaha-usaha dalam bidang Pembiayaan Multiguna, yaitu pembiayaan untuk pengadaan barang
              dan/atau jasa yang diperlukan oleh debitur untuk pemakaian/konsumsi dan bukan untuk keperluan
              usaha (aktivitas produktif) dalam jangka waktu yang diperjanjikan, yang wajib dilakukan dengan cara:

              <ol type="1" class="sf-list-style">
                <li>Sewa Pembiayaan (Finance Lease).</li>
                <li>Pembelian Dengan Pembayaran Secara Angsuran.</li>
                <li>Pembiayaan lain setelah terlebih dahulu mendapatkan persetujuan dari Otoritas Jasa Keuangan.</li>
              </ol>

            </li>
            <li class="mt-3">Kegiatan usaha pembiayaan lain berdasarkan persetujuan Otoritas Jasa Keuangan.</li>
          </ol>
          </p>
        </div>
        <div class="mt-3">
          <p class="sf-title">Social Media Kami</p>
          <div class="sf-social-media mt-2">
            <a href="#"><img src="{{ asset('assets/svg/web.svg') }}" alt="SANF Web" draggable="false" /></a>
            <a href="#"><img src="{{ asset('assets/svg/ig.svg') }}" alt="SANF Instagram" draggable="false" /></a>
            <a href="#"><img src="{{ asset('assets/svg/linkedin.svg') }}" alt="SANF Linkedin" draggable="false" /></a>
          </div>
        </div>
        <div class="mt-3">
          <p class="sf-title">Terdaftar dan diawasi oleh</p>
          <div class="sf-ojk mt-2">
            <img src="{{ asset('assets/png/ojk.png') }}" alt="SANF Web" draggable="false" />
          </div>
        </div>
        <div class="mt-3" style="width: 100%;">
          <div class="sf-satu-indonesia mt-2">
            <img src="{{ asset('assets/png/satu-indonesia.png') }}" alt="SANF Web" draggable="false" />
          </div>
        </div>
      </div>
    </div>
  </div>

</body>

</html>