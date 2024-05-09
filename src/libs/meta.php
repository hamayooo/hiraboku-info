<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" >
<meta name="format-detection" content="telephone=no">

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-1932775-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-1932775-1');
</script>
<!-- Viewport
======================================================================-->
<?php
	require_once 'ua.class.php';
	$ua = new UserAgent();
	if($ua->set() === 'tablet') :
		$width = '1124px';
	elseif($ua->set() === 'mobile') :
		$width = '375px';
?>
<meta content="width=<?php echo $width; ?>" name="viewport">
<?php else: ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<?php endif; ?>


<!-- SEO
======================================================================-->
<?php include(APP_PATH.'libs/argument.php');  ?>
<title><?php echo $seo_title?></title>
<meta name="description" content="<?php echo $seo_description; ?>">
<meta name="keywords" content="<?php echo $seo_keyword; ?>">


<!-- Facebook
======================================================================-->
<meta property="og:title" content="<?php echo $seo_title?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].$_SERVER["QUERY_STRING"];?>">
<meta property="og:image" content="<?php echo APP_URL;?>images/favicons/ogp.png">
<meta property="og:site_name" content="平田牧場公式サイト">
<meta property="og:description" content="<?php echo $seo_description; ?>" />
<meta property="fb:app_id" content="437767167507590">


<!-- CSS
======================================================================-->

<!-- Slick ======================================================================-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" rel="stylesheet">

<!-- Lightbox ======================================================================-->
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.min.css" rel="stylesheet"> -->
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700;900&family=Noto+Serif+JP:wght@300;400;500;600;700&family=Vollkorn:wght@400;500;600;700&family=Roboto:wght@500&family=Barlow:wght@500&display=swap" rel="stylesheet">
<link href="<?php echo APP_URL;?>css/style.css" rel="stylesheet">


<!-- Favicon
======================================================================-->
<link rel="icon" href="<?php echo APP_URL;?>images/favicons/favicon.ico">
<link rel="shortcut icon" href="<?php echo APP_URL;?>images/favicons/favicon.ico">
<link rel="apple-touch-icon" href="<?php echo APP_URL;?>images/favicons/apple-touch-icon.png">


<!-- IE Hack
======================================================================-->
<!--[if lt IE 9]>
<script src="//cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!--  Google Tag Manager
======================================================================-->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KRN8FQZ');</script>