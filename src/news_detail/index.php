<?php
$path = realpath(dirname(__FILE__) . '') . "/../";
include_once $path . 'app_config.php';
include $path . 'libs/meta.php';

?>
</head>

<body class="news news_detail">



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
				<span class="pc">ニュース / News</span>
				<strong class="sp">ニュース</strong>
				<small class="sp">News</small>
			</h2>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span><a href="#">HOME</a></span>
					</li>
					<li>
						<span><a href="#">ニュース</a></span>
					</li>
					<li>
						<span>ニュースタイトル</span>
					</li>
				</ul>
			</div>
		</div>



		<div class="content">
			<div class="inner">


				<div class="left">
					<div class="date-cate">
						<div class="date">
							<small>2021.</small>01.04
						</div>
						<div class="cate">
							<span class="label">キャンペーン</span>
						</div>
					</div>
					<h3 class="title">タイトル　例｜特製恵方巻　ご予約承り中【平田牧場 直営店】　</h3>
					<div class="contents">
						<h4 class="subtitle">サブタイトル１　例｜今年の節分は2月2日（火）平田牧場　特製恵方巻　ご予約承り中！</h4>
						<img src="../images/store_detail/dammy.png" alt="">
						<h5>サブタイトル2　例｜今年の節分は2月2日（火）平田牧場　特製恵方巻　ご予約承り中！</h5>
						<p>ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。ニュースタイトルが入ります。</p>
						<p><a href="#">リンク</a>が入った場合のデザイン。リンクが入った場合のデザイン。リンクが入った場合のデザイン。リンクが入った場合のデザイン。</p>
						<ul>
							<li>リストのデザイン</li>
							<li>リストのデザイン</li>
							<li>リストのデザイン</li>
						</ul>
					</div>
					<div class="pagenavi">
						<a href="#" class="prev">
							<strong>前の記事</strong>
							<p>前の記事のタイトルが入ります。前の記事のタイトルが入ります。前の記事のタイトルが入ります。前の記事のタイトルが入ります。</p>
						</a>
						<a href="#" class="all"><strong>ニュース一覽へ</strong></a>
						<a href="#" class="next">
							<strong>次の記事</strong>
							<p>前の記事のタイトルが入ります。前の記事のタイトルが入ります。前の記事のタイトルが入ります。前の記事のタイトルが入ります。</p>
						</a>
					</div>
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
