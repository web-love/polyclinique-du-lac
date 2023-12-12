<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head prefix="og: http://ogp.me/ns#"> <!--OPEN GRAPH PROTOCOL-->
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-579D2R3');</script>
    <!-- End Google Tag Manager -->
    
    <!--ASSETS-->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/patch/patch-above-fold.min.css?rel=6b6d99c6de">
    <!--/ASSETS-->

    <!--SEO-->
        <!-- Search engine -->
        <?php $background_image = get_field('background', get_the_ID());  ?>
        <?php if( is_array($background_image) && isset($background_image['sizes']['1000x500']) ): ?>
		    <meta property="og:image" content="<?php echo $background_image['sizes']['1000x500'] ?>" />
		<?php else: ?>
			<meta property="og:image" content="<?php bloginfo('template_directory'); ?>/img/polyclinique_du_lac-FB.jpg" />
		<?php endif; ?>
    <!--/SEO-->

	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

    <?php wp_head(); ?>

    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/styles/style.min.css?rel=96c2e9b9b4">
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/styles/owl.carousel.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/styles/patch.css"> 
<!-- Global Site Tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-25412541-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments)};
  gtag('js', new Date());

  gtag('config', 'UA-25412541-1');
</script>
<script src="https://kit.fontawesome.com/4e35a34801.js" crossorigin="anonymous"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-579D2R3"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
