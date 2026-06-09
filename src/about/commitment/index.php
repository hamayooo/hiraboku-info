<?php
$path = realpath(dirname(__FILE__) . '') . "/../../";
include_once $path . 'app_config.php';
include $path . 'libs/meta.php';

?>
</head>

<body class="commitment">



<!-- Header
======================================================================-->
<?php include $path . 'libs/header.php';?>



<!-- Wrap
======================================================================-->
<div class="wrap">



	<!-- Main Content
	======================================================================-->
	<main>

		<div class="mv-sub">
			<h1>
				<strong>コミットメント</strong>
				<small>Commitment</small>
			</h1>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span><a href="<?php echo APP_URL;?>">HOME</a></span>
					</li>
					<li>
						<span><a href="<?php echo APP_URL;?>about/">平田牧場について</a></span>
					</li>
					<li>
						<span>コミットメント</span>
					</li>
				</ul>
			</div>
		</div>

        <section class="section01">
            <div class="inner">
                <h2>
                    <p>わたしたちはより豊かな食生活、<br class="sp">食文化を提案する</p>
                    <p><span>【健康創造企業】</span></p>
                    <p>をめざします。</p>
                </h2>
                <div class="img"><img src="<?php echo APP_URL; ?>images/about/illust.png" alt=""></div>
                <span>平田牧場グループは、より豊かな食生活・食文化を提案する“健康創造企業”として、<br class=pc>
                    おいしく健康に良い食材の生産やお客様に高いご満足を提供できるよう、<br class="pc">
                    力を尽くしてまいります。</span>

                <div class="images sp">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img01.jpg" alt="">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img02.jpg" alt="">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img03.jpg" alt="">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img04.jpg" alt="">
                </div>
            </div>
                <div class="images pc">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img01.jpg" alt="">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img02.jpg" alt="">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img03.jpg" alt="">
                    <img src="<?php echo APP_URL; ?>images/about/commitment/img04.jpg" alt="">
                </div>
        </section>

        <section class="section02">
            <div class="inner">
                <div class="img"><img src="<?php echo APP_URL; ?>images/about/commitment/img05.jpg" alt=""></div>
                <a href="/about/commitment/health/" class="inner01">
                    <h2>無添加へのこだわり</h2>
                    <p>疑わしいものは使用しない</p>
                    <span>私たちは創業以来、 『おいしくて体に良いものを作る』<br class="pc">
                    という強い信念を守り続け、化学調味料一切不使用という安全安心を徹底して参りました。<br><br>
                    お客様の声から始まった無添加ハム・ソーセージづくり。<br class="pc">
                    そして、素材の美味しさを十分に引き出す製品づくりについてご紹介します。<br>
                    自分たちの手で企画開発し、生産・流通の管理から販売まで。平田牧場こだわりの一貫生産体制についてご紹介します。</span>
                		<div class="text"><span class="more">もっと見る</span></div>
                </a>
            </div>
        </section>

        <section class="section03">
            <div class="inner">
                <div class="img"><img src="<?php echo APP_URL; ?>images/about/commitment/img06.jpg" alt=""></div>
                    <a class="inner01" href="/about/commitment/sdgs/">
                        <h2>SDGsへの取り組み</h2>
                        <p>人だけでなく<br class="sp">社会も健康にしたい</p>
                        <span>私たちは山形県庄内で休耕田を利用して飼料用米をつくり、豚の飼料に活かす環境保全型の取り組みを20年以上も前から進めてきました。2010年にはすべての豚を飼料用米で育てるまでとなりました。<br>
                        さらに、排泄物は良質な完熟有機肥料に仕上げて地元農家でご利用いただき、資源循環型の農業環境づくりに貢献しています。<br>
                        昔ながらの畜・農が連携する自然な方法で資源の循環をさらに推し進め、日本の美しい自然環境を守り、持続可能な社会への取り組みを加速させていきます。</span>
                    		<div class="text"><span class="more">もっと見る</span></div>
                		</a>
            </div>
        </section>
        <a href="<?php echo APP_URL; ?>about/" class="link page-back">平田牧場について に戻る</a>


	</main>


	<!-- Footer
	======================================================================-->
	<?php include $path . 'libs/online.php';?>

	<!-- Footer
	======================================================================-->
	<?php include $path . 'libs/footer.php';?>



</div>



<!-- Scripts
======================================================================-->
<?php include $path . 'libs/scripts.php';?>
</body>
</html>
