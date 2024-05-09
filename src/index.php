<?php
$path = realpath(dirname(__FILE__) . '') . "/";
include_once $path . 'app_config.php';
include $path . 'libs/meta.php';

?>
</head>

<body class="index">



<!-- Header
======================================================================-->
<?php include $path . 'libs/header.php';?>



<!-- Wrap
======================================================================-->
<div class="wrap">



	<!-- Main Content
	======================================================================-->
	<main>

		<div class="mv">
			<div class="mv__slider">
				<div class="slide" style="background: url(./images/top/mv01.jpg)center center/cover no-repeat;"></div>
				<div class="slide" style="background: url(./images/top/mv02.jpg)center center/cover no-repeat;"></div>
				<div class="slide" style="background: url(./images/top/mv03.jpg)center center/cover no-repeat;"></div>
			</div>
			<div class="mv__text">
				<div class="inner">
					<div class="text">
						<img class="pc" src="./images/top/text_mv.svg" alt="健康へ。信じられるものだけを。">
						<img class="sp" src="./images/top/text_mv_sp.svg" alt="健康へ。信じられるものだけを。">
					</div>
				</div>
			</div>
			<div class="mv__bottom">
				<a href="#">
					<i class="pc">
						<img src="./images/common/arrow-left.svg" alt="">
					</i>
					<i class="sp">
						<img src="./images/common/icon_store.svg" alt="">
					</i>
					<div class="text">
						<span class="en pc">Store Information</span>
						<span class="jp">平田牧場店舗一覧</span>
					</div>
				</a>
				<a href="#">
					<i class="sp">
						<img src="./images/common/icon_cart.svg" alt="">
					</i>
					<div class="text">
						<span class="en pc">Online Shop</span>
						<span class="jp">オンラインショップ</span>
					</div>
					<i class="pc">
						<img src="./images/common/arrow-right.svg" alt="">
					</i>
				</a>
			</div>
		</div>



		<section class="news">
			<div class="inner">
				<div class="news__title">
					<h2>
						<strong>新着情報</strong>
						<small>News</small>
					</h2>
				</div>
				<div class="news__box">
					<ul>
						<li>
							<a href="#">
								<div class="date">
									<small>2021.</small>
									<span>01.04</span>
								</div>
								<div class="text">
									<span class="label olv">キャンペーン</span>
									<p class="pc">2021年数量限定福袋の店頭販売について</p>
								</div>
								<p class="sp">2021年数量限定福袋の店頭販売について</p>
							</a>
						</li>
						<li>
							<a href="#">
								<div class="date">
									<small>2021.</small>
									<span>01.04</span>
								</div>
								<div class="text">
									<span class="label olv">キャンペーン</span>
									<p class="pc">2021年数量限定福袋の店頭販売について</p>
								</div>
								<p class="sp">2021年数量限定福袋の店頭販売について</p>
							</a>
						</li>
					</ul>
					<a href="#" class="more">もっと見る</a>
				</div>
			</div>
		</section>


		<section class="about">
			<div class="inner">
				<div class="about__title">
					<div class="logo">
						<img src="./images/common/logo_white.svg" alt="">
					</div>
					<h2>平田牧場について</h2>
				</div>
				<div class="about__content">
					<div class="box box01">
						<h3>
							<strong>いちばん丁寧な<br>ブランドになろう</strong>
							<small>Brand statement</small>
						</h3>
						<div class="img">
							<img src="./images/top/img01.jpg" alt="">
						</div>
						<div class="text">
							<p>
								私たち平田牧場は、品種開発や子豚生産、肥育から加工・流通・販売に至るまで、自社で行っている会社です。いのちに感謝する日本の食のDNAを受け継いで、いちばん丁寧にいのちと向き合うブランドになろう。これまでも、これからも。平田牧場の変わらない想いです。
							</p>
						</div>
						<a href="#" class="more">もっと見る</a>
					</div>
					<div class="box-wrap">
						<div class="box box02">
							<h3>
								<strong>健康創造企業へ</strong>
								<small>Health Initiatives</small>
							</h3>
							<div class="text">
								<p>
									平田牧場グループは、より豊かな食生活・食文化を提案する“健康創造企業”として、おいしく健康に良い食材の生産やお客様に高いご満足を提供できるよう、力を尽くしてまいります。
								</p>
							</div>
							<a href="#" class="more">もっと見る</a>
						</div>
						<div class="box box03">
							<h3>
								<strong>SDGsへの取り組み</strong>
								<small>Approach to SDGs</small>
							</h3>
							<div class="img img03">
								<img src="./images/top/img02.svg" alt="">
							</div>
							<div class="text">
								<p>
									平田牧場は持続可能な開発目標【SDGs】を支援してます。
								</p>
							</div>
							<a href="#" class="more sp">もっと見る</a>
						</div>
					</div>
				</div>
				<a href="#" class="more wht">ABOUTへ</a>
			</div>
		</section>


		<section class="brand">
			<div class="inner">
				<div class="brand__box">
					<div class="icon pc">
						<img src="./images/top/logo_brand01.png" alt="">
					</div>
					<div class="center">
						<h2>
							<strong>金華豚と三元豚</strong>
							<small>Brand</small>
						</h2>
						<div class="icon-list sp">
							<div class="icon">
								<img src="./images/top/logo_brand01.png" alt="">
							</div>
							<div class="icon">
								<img src="./images/top/logo_brand02.png" alt="">
							</div>
						</div>
						<p>平田牧場三元豚と平田牧場金華豚は、たくさんのこだわりがつまった美味しいブランド豚。<br>それぞれのブランド豚の特徴や、その育て方についてご紹介します。</p>
						<a href="#" class="more">もっと見る</a>
					</div>
					<div class="icon pc">
						<img src="./images/top/logo_brand02.png" alt="">
					</div>
				</div>
			</div>
		</section>


		<section class="store">
			<div class="inner">
				<div class="store__title">
					<h2>
						<strong>店舗を探す</strong>
						<small>Store Information</small>
					</h2>
				</div>
				<div class="store__content">
					<h3>エリア別</h3>
					<div class="area">
						<div class="area__box">
							<div class="img img01">
								<img src="./images/common/area01.svg" alt="">
							</div>
							<div class="area-list list01">
								<small>東北地方</small>
								<ul>
									<li>
										<a href="#">山形</a>
									</li>
									<li>
										<a href="#">宮城</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="area__box">
							<div class="img img02">
								<img src="./images/common/area02.svg" alt="">
							</div>
							<div class="area-list list02">
								<strong>関東地方</strong>
								<ul>
									<li>
										<a href="#">東京</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<h3>目的別</h3>
					<div class="store-list">
						<a href="#">
							<div class="img" style="background: url(./images/common/store01.jpg)center center/cover no-repeat;"></div>
							<div class="text">
								<span>飲食店</span>
							</div>
						</a>
						<a href="#">
							<div class="img" style="background: url(./images/common/store02.jpg)center center/cover no-repeat;"></div>
							<div class="text">
								<span>物販店</span>
							</div>
						</a>
					</div>
					<a href="#" class="more">店舗一覧へ</a>
				</div>
			</div>
		</section>


		<section class="recruit">
			<div class="inner">
				<div class="recruit__box">
					<h2>
						<strong>採用情報</strong>
						<small>Recruit</small>
					</h2>
					<div class="img sp">
						<img src="./images/top/bg05_sp.jpg" alt="">
					</div>
					<div class="text pc">
						<p>
							平田牧場では一緒に働く仲間を募集しています。<br>
							I/Uターンも歓迎、<br>
							インターンシップも随時開催しています。
						</p>
						<a href="#" class="rec">平田牧場採用サイト</a>
					</div>
				</div>
			</div>
		</section>


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
