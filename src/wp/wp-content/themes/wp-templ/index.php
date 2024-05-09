<?php
$path = realpath(dirname(__FILE__) . '') . "/../../../../";
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
			<?php if(have_rows('slide', 55)): ?>
			<div class="mv__slider">
				<?php while(have_rows('slide', 55)): the_row(); ?>
				<a href="<?php echo get_sub_field('url'); ?>" target="_blank" class="slide" style="background: url(<?php echo get_sub_field('mv'); ?>)center center/cover no-repeat;"></a>
				<?php endwhile; ?>
			</div>
			<?php endif;?>
			<div class="mv__text pc">
				<div class="inner">
					<div class="text">
						<h1>
							<span>健康へ。</span><br>
							<span>信じられる</span><br>
							<span>ものだけを。</span>
						</h1>
					</div>
				</div>
			</div>
			<div class="mv__bottom">
				<a href="<?php echo APP_URL;?>shop/">
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
				<a href="https://www.hiraboku.com/" target="_blank">
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
					<?php
						$args=array(
							'post_type' => 'news',
							// 'orderby' => 'post_date',
							'posts_per_page'=> 5,
						);
						$query = new WP_Query( $args );
						if($query -> have_posts()):
					?>
					<ul>
						<?php while($query -> have_posts()): $query->the_post();?>
						<li>
							<a href="<?php the_permalink(); ?>">
								<div class="date">
									<small><?php the_time('Y'); ?>.</small>
									<span><?php the_time('m.d'); ?></span>
								</div>
								<div class="text">
									<div class="cate">
										<?php
											if ($terms = get_the_terms($post->ID, 'newscat')) :
											foreach ( $terms as $term ) :
											$term_name = $term->name;
											$color = ColorfulCategories::getColorForTerm($term->term_id, true);
										?>
										<span class="label" style="background-color:<?php echo esc_attr($color); ?>;"><?php echo $term_name; ?></span>
										<?php endforeach; endif;?>
									</div>
									<p class="pc"><?php the_title(); ?></p>
								</div>
								<p class="sp"><?php the_title(); ?></p>
							</a>
						</li>
						<?php endwhile; ?>
					</ul>
					<?php endif; ?>
					<a href="./news/" class="more">もっと見る</a>
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
					<a href="./about/brand/" class="box box01">
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
						<span class="more">もっと見る</span>
					</a>
					<div class="box-wrap">
						<a href="./about/commitment/health/" class="box box02">
							<h3>
								<strong>健康創造企業へ</strong>
								<small>Health Initiatives</small>
							</h3>
							<div class="text">
								<p>
									平田牧場グループは、より豊かな食生活・食文化を提案する“健康創造企業”として、おいしく健康に良い食材の生産やお客様に高いご満足を提供できるよう、力を尽くしてまいります。
								</p>
							</div>
							<span class="more">もっと見る</span>
						</a>
						<a href="./about/commitment/sdgs/" class="box box03">
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
							<span class="more">もっと見る</span>
						</a>
					</div>
				</div>
				<a href="./about/" class="more wht">平田牧場について</a>
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
						<a href="./about/brand/" class="more">もっと見る</a>
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
										<a href="<?php echo APP_URL;?>areacat/yamagata#search-text">山形</a>
									</li>
									<li>
										<a href="<?php echo APP_URL;?>areacat/miyagi#search-text">宮城</a>
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
										<a href="<?php echo APP_URL;?>areacat/tokyo#search-text">東京</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<h3>目的別</h3>
					<div class="store-list">
						<a href="<?php echo APP_URL;?>storecat/eat#search-text">
							<div class="img" style="background: url(./images/common/store01.jpg)center center/cover no-repeat;"></div>
							<div class="text">
								<span>飲食店</span>
							</div>
						</a>
						<a href="<?php echo APP_URL;?>storecat/product#search-text">
							<div class="img" style="background: url(./images/common/store02.jpg)center center/cover no-repeat;"></div>
							<div class="text">
								<span>物販店</span>
							</div>
						</a>
					</div>
					<a href="<?php echo APP_URL;?>shop/" class="more">店舗一覧へ</a>
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
						<a href="https://recruit.hiraboku.info/" target="_blank" class="rec">平田牧場採用サイト</a>
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
