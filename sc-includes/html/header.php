<!doctype html>

<html lang="<?= getConfParam('LANGUAGE'); ?>" xmlns:fb="http://ogp.me/ns/fb#" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">

<head>
  <base href="<?= getConfParam('SITE_URL'); ?>">
  <? getCanonical(); ?>
  <!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <link rel="shortcut icon" type="image/x-icon" href="<?= getConfParam('SITE_URL'); ?><?= IMG_PATH; ?>favicon.ico">
  <title><?= $TITLE_; ?></title>
  <?php if (DEBUG): ?>
    <meta name="robots" content="noindex,nofollow">
  <?php endif ?>
  <meta name="title" content="<?= $TITLE_; ?>" />
  <meta name="description" content="<?= $DESCRIPTION_; ?>" />
  <meta name="keywords" content="<?= $KEYWORDS_; ?>">
  <meta property="og:title" content="<?= $TITLE_; ?>">
  <meta property="og:description" content="<?= $DESCRIPTION_; ?>">
  <meta property="og:site_name" content="<?= getConfParam('SITE_NAME'); ?>">
  <meta property="og:url" content="<?= trim(getConfParam('SITE_URL'), '/'); ?><?= $_SERVER['REQUEST_URI']; ?>">
  <meta property="og:type" content="<?= $TYPE_SITE; ?>">
  <? getImageHead(); ?>
  <!--<link rel="stylesheet" media="all" type="text/css" href="<?= getConfParam('SITE_URL'); ?><?= CSS_PATH; ?>fonts/font-awesome.min.css">-->

  <link rel="preload" href="src/css/select2.min.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
  <noscript>
    <link rel="stylesheet" href="src/css/select2.min.css">
  </noscript>
  <!-- <link rel="stylesheet" href="src/css/all.min.css"> -->
  <link rel="stylesheet" href="src/css/webfonts/fuentes.css">



  <!--- css -->
  <!-- <link rel="preload" href="src/css/bootstrap.min.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
<noscript><link rel="stylesheet" href="src/css/bootstrap.min.css"></noscript> -->

  <link rel="stylesheet" href="src/css/bootstrap.min.css">

  <!-- <script src="src/js/glide.min.js"></script> -->


  <!-- js -->
  <script defer src="src/js/splide.min.js"></script>
  <!-- <script src="src/js/jquery.min.js"></script> -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="src/js/jquery-ui.min.js"></script>
  <script defer src="src/js/bootstrap.min.js"></script>

  <!-- <link rel="stylesheet" type="text/css" href="src/css/cookies.css"> -->
  <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"> -->
  <!-- <link rel="alternate" type="application/rss+xml" title="<?= $TITLE_; ?>" href="<?= getConfParam('SITE_URL'); ?>feed/"> -->
  <!--<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>-->

  <link rel="stylesheet" href="src/css/style.css?v=0.6">
  <link rel="stylesheet" href="src/css/main.css?=v=0.5">
  <link rel="stylesheet" type="text/css" href="src/css/w-formPost.css?v=0.2">

  <link rel="preload" href="src/css/item.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
  <noscript>
    <link rel="stylesheet" href="src/css/item.css">
  </noscript>

  <link rel="preload" href="src/css/splide.min.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
  <noscript>
    <link rel="stylesheet" href="src/css/splide.min.css">
  </noscript>


  <script defer src="src/js/select2.js"></script>
  <script defer type="text/javascript" src="<?= getConfParam('SITE_URL'); ?><?= JS_PATH; ?>jquery.sortable.min.js"></script>
  <script defer type="text/javascript" src="<?= getConfParam('SITE_URL'); ?><?= JS_PATH; ?>jquerynumeric.js"></script>

  <script type="text/javascript">
    var site_url = '<?= getConfParam('SITE_URL'); ?>';
  </script>
  <script src="src/js/main.js?v=0.2" defer></script>
  <!--<link rel="stylesheet" href="node_modules/@glidejs/glide/dist/css/glide.core.min.css">-->
  <!--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>-->
  <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide">-->
  <style>
    .splide {
      visibility: hidden;
      position: relative;
    }
  </style>

  <?php DE::init() ?>
</head>

<body>
  <? if (getConfParam('ANALYTICS_ID') !== "") { ?>
    <script>
      (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
          (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
          m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
      })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

      ga('create', 'UA-<?= getConfParam('ANALYTICS_ID') ?>', 'auto');
      ga('send', 'pageview');
    </script>
  <? } ?>