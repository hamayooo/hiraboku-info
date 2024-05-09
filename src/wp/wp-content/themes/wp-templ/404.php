<?php
$path = realpath(dirname(__FILE__) . '') . "/../../../../";
include_once $path . 'app_config.php';
include $path . 'libs/meta.php';
?>
</head>
<body id="page404" class='404'>
    <!-- Header
    ================================================== -->
    <?php include(APP_PATH.'/libs/header.php'); ?>

    <style>
	    html{
	        margin: 0 !important;
	    }
	    #page404 .taC{
	        display: block;
	        font-size: 16px;
	        line-height: 28px;
	        letter-spacing: 1px;
	        text-align: center;
	        margin: 20px 0 0 0;
	    }
	    #page404 .taC a{font-size: 20px;text-decoration: none;}
	    #page404 .page-title{
	        font-size: 30px;
	        font-weight: bold;
	        letter-spacing: 5px;
	        padding-bottom: 50px;
          text-align: center;
          line-height: 1.5;
	    }
	    .page_404{
	        padding: 200px 0 200px;
	    }
	</style>


        <main>
            <section class="page_404">
                <h2 class="page-title">お探しのページは<br class="sp">見つかりません</h2>
                <div class='taC'>
                    <!-- /mainContent start -->
                    <p>
                    お手数ですが、上のメニューまたは<br class="sp">下のボタンから再度お探しください。
                    <br>
                    <img src="../images/common/pig404.svg" alt="404pig">
                    <a href="<?php echo esc_url( home_url( '/' ) )?>" class="link page-back">トップに戻る</a>
                    </p>

                    <!-- /maincontent end -->
                </div>
            </section>

        </main>
    <!-- Footer
    ================================================== -->
    <?php include(APP_PATH.'/libs/footer.php'); ?>
<!-- End Document
================================================== -->
</body>
</html>
