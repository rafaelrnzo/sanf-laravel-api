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
      color: #000000;
      margin: 0;
      text-transform: uppercase;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    .sf-text {
      font-family: 'Gilroy';
      font-style: normal;
      font-weight: normal;
      font-size: 14px;
      line-height: 150%;
      color: #999BAC;
      margin-bottom: 0;
      margin-top: 10px;
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
        <h3 class="sf-title">Syarat & Ketentuan Umum</h3>
        <div>
          <h3 class="sf-title">1. Your Agreement</h3>
          <p class="sf-text">By using this Site, you agree to be bound by, and to comply with, these Terms and Conditions.
            If you do not agree to these Terms and Conditions, please do not use this site.
            <br /><br />
            PLEASE NOTE: We reserve the right, at our sole discretion, to change, modify or otherwise alter these
            Terms and Conditions at any time. Unless otherwise indicated, amendments will become effective immediately.
            Please review these Terms and Conditions periodically. Your continued use of the Site following the posting
            of changes and/or modifications will constitute your acceptance of the revised Terms and Conditions and
            the reasonableness of these standards for notice of changes. For your information, this page was last updated
            as of the date at the top of these terms and conditions.
          </p>
        </div>
        <div class="mt-3">
          <h3 class="sf-title">2. Privacy</h3>
          <p class="sf-text">Please review our Privacy Policy, which also governs your visit to this Site, to understand our practices.</p>
        </div>
        <div class="mt-3">
          <h3 class="sf-title">3. Linked Sites</h3>
          <p class="sf-text">This Site may contain links to other independent third-party Web sites ("Linked Sites”).
            These Linked Sites are provided solely as a convenience to our visitors. Such Linked Sites are not under our control,
            and we are not responsible for and does not endorse the content of such Linked Sites, including any information or materials
            contained on such Linked Sites. You will need to make your own independent judgment regarding your interaction with these
            Linked Sites.
          </p>
        </div>
      </div>
    </div>
  </div>

</body>

</html>