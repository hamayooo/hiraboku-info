<?php
$path = realpath(dirname(__FILE__) . '') . "/../";
include_once $path . 'app_config.php';
include $path . 'libs/meta.php';

?>
</head>

<body class="news">



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
			<h2>
				<strong>ニュース</strong>
				<small>News</small>
			</h2>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span>HOME</span>
					</li>
					<li>
						<span>ニュース</span>
					</li>
				</ul>
			</div>
		</div>



		<div class="content">
			<div class="inner">
				<div class="left">
					<ul class="news-list">
						<li>
							<a href="#">
								<div class="img" style="background: url(../images/store_detail/dammy.png)center center/cover no-repeat;"></div>
								<div class="text">
									<div class="date-cate">
										<div class="date">
											<small>2021.</small>01.04
										</div>
										<div class="cate">
											<span class="label">キャンペーン</span>
										</div>
									</div>
									<p class="title">ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。</p>
								</div>
							</a>
						</li>
						<li>
							<a href="#">
								<div class="img" style="background: url(../images/store_detail/dammy.png)center center/cover no-repeat;"></div>
								<div class="text">
									<div class="date-cate">
										<div class="date">
											<small>2021.</small>01.04
										</div>
										<div class="cate">
											<span class="label">キャンペーン</span>
										</div>
									</div>
									<p class="title">ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。</p>
								</div>
							</a>
						</li>
						<li>
							<a href="#">
								<div class="img" style="background: url(../images/store_detail/dammy.png)center center/cover no-repeat;"></div>
								<div class="text">
									<div class="date-cate">
										<div class="date">
											<small>2021.</small>01.04
										</div>
										<div class="cate">
											<span class="label">キャンペーン</span>
										</div>
									</div>
									<p class="title">ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。</p>
								</div>
							</a>
						</li>
						<li>
							<a href="#">
								<div class="img" style="background: url(../images/store_detail/dammy.png)center center/cover no-repeat;"></div>
								<div class="text">
									<div class="date-cate">
										<div class="date">
											<small>2021.</small>01.04
										</div>
										<div class="cate">
											<span class="label">キャンペーン</span>
										</div>
									</div>
									<p class="title">ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。</p>
								</div>
							</a>
						</li>
						<li>
							<a href="#">
								<div class="img" style="background: url(../images/store_detail/dammy.png)center center/cover no-repeat;"></div>
								<div class="text">
									<div class="date-cate">
										<div class="date">
											<small>2021.</small>01.04
										</div>
										<div class="cate">
											<span class="label">キャンペーン</span>
										</div>
									</div>
									<p class="title">ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。</p>
								</div>
							</a>
						</li>
					</ul>
					<nav class="pagination">
						<div class="wp-pagenavi">
							<a href="#" class="page prev"></a>
							<span class="page current">1</span>
							<a href="#" class="page larger">2</a>
							<a href="#" class="page next"></a>
						</div>
					</nav>
				</div>
				<div class="sidebar">
					<div class="sidebar-wrap">
						<div class="box">
							<strong>カテゴリー</strong>
							<ul>
								<li>
									<a href="#">すべて</a>
								</li>
								<li>
									<a href="#">ニュース</a>
								</li>
								<li>
									<a href="#">キャンペーン</a>
								</li>
								<li>
									<a href="#">イベント</a>
								</li>
							</ul>
						</div>
						<div class="box box02">
							<strong>アーカイブ</strong>
							<ul>
								<li>
									<span class="trigger">2021</span>
									<ul>
										<li>
											<a href="#">-12月</a>
										</li>
										<li>
											<a href="#">-01月</a>
										</li>
									</ul>
								</li>
								<li>
									<span class="trigger">2020</span>
									<ul>
										<li>
											<a href="#">-12月</a>
										</li>
										<li>
											<a href="#">-01月</a>
										</li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>












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
