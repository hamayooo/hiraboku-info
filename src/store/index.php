<?php
$path = realpath(dirname(__FILE__) . '') . "/../";
include_once $path . 'app_config.php';
include $path . 'libs/meta.php';

?>
</head>

<body class="store">



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
				<strong>店舗一覧</strong>
				<small>Store Information</small>
			</h2>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span>HOME</span>
					</li>
					<li>
						<span>店舗一覧</span>
					</li>
				</ul>
			</div>
		</div>




		<div class="search">
			<div class="inner">
				<div class="search__tab">
					<ul>
						<li class="active" data-triger="0">エリアで探す</li>
						<li data-triger="1">目的別に探す</li>
					</ul>
				</div>
				<div class="search__content">
					<div class="tab-content is-active">
						<div class="area-list">
							<div class="area">
								<div class="img img01">
									<img src="../images/common/area01.svg" alt="">
								</div>
								<div class="text text01">
									<strong>東北地方</strong>
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
							<div class="area">
								<div class="img img02">
									<img src="../images/common/area02.svg" alt="">
								</div>
								<div class="text text02">
									<strong>関東地方</strong>
									<ul>
										<li>
											<a href="#">東京</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-content tab-content02">
						<div class="store-list">
							<a href="#">
								<div class="img" style="background: url(../images/common/store01.jpg)center center/cover no-repeat;"></div>
								<div class="text">
									<span>飲食店</span>
								</div>
							</a>
							<a href="#">
								<div class="img" style="background: url(../images/common/store02.jpg)center center/cover no-repeat;"></div>
								<div class="text">
									<span>物販店</span>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="content">
			<section class="area-box">
				<div class="inner">
					<h3>東北地方</h3>
					<div class="area-box__box">
						<div class="area01">
							<span>山形県</span>
						</div>
						<div class="detail">
							<ul class="detail__list">
								<li>
									<div class="img-wrap">
										<div class="img" style="background: url(../images/store/img01.jpg)center center/cover no-repeat;"></div>
										<div class="cate-list">
											<span class="label brw">飲食店</span>
											<span class="label grn">物販店</span>
										</div>
									</div>
									<div class="text">
										<strong>とんかつと豚肉料理   平田牧場 ホテルメトロポリタン山形店</strong>
										<div class="address">
											<address>
												山形県山形市香澄町1-1-1 ホテルメトロポリタン山形2F
											</address>
											<a href="#" class="googlemap">
												<span class="pc">Google Map</span>
												<img class="sp" src="../images/common/icon_gmap.svg" alt="">
											</a>
										</div>
										<table>
											<tr>
												<th>TEL.</th>
												<td>023‐679‐5929</td>
											</tr>
											<tr>
												<th>営業時間</th>
												<td>11:00〜21:00（L.O 20:00）</td>
											</tr>
										</table>
										<div class="cate-list">
											<span class="cate">とんかつ</span>
											<span class="cate">豚肉料理</span>
											<span class="cate">GotoEat対象店舗</span>
											<span class="cate">GoToトラベル地域共通クーポン取扱店舗</span>
										</div>
									</div>
								</li>
								<li>
									<div class="img-wrap">
										<div class="img" style="background: url(../images/store/img01.jpg)center center/cover no-repeat;"></div>
										<div class="cate-list">
											<span class="label brw">飲食店</span>
											<span class="label grn">物販店</span>
										</div>
									</div>
									<div class="text">
										<strong>とんかつと豚肉料理   平田牧場 ホテルメトロポリタン山形店</strong>
										<div class="address">
											<address>
												山形県山形市香澄町1-1-1 ホテルメトロポリタン山形2F
											</address>
											<a href="#" class="googlemap">
												<span class="pc">Google Map</span>
												<img class="sp" src="../images/common/icon_gmap.svg" alt="">
											</a>
										</div>
										<table>
											<tr>
												<th>TEL.</th>
												<td>023‐679‐5929</td>
											</tr>
											<tr>
												<th>営業時間</th>
												<td>11:00〜21:00（L.O 20:00）</td>
											</tr>
										</table>
										<div class="cate-list">
											<span class="cate">とんかつ</span>
											<span class="cate">豚肉料理</span>
											<span class="cate">GotoEat対象店舗</span>
											<span class="cate">GoToトラベル地域共通クーポン取扱店舗</span>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="area-box__box">
						<div class="area01">
							<span>山形県</span>
						</div>
						<div class="detail">
							<ul class="detail__list">
								<li>
									<div class="img-wrap">
										<div class="img" style="background: url(../images/store/img01.jpg)center center/cover no-repeat;"></div>
										<div class="cate-list">
											<span class="label brw">飲食店</span>
											<span class="label grn">物販店</span>
										</div>
									</div>
									<div class="text">
										<strong>とんかつと豚肉料理   平田牧場 ホテルメトロポリタン山形店</strong>
										<div class="address">
											<address>
												山形県山形市香澄町1-1-1 ホテルメトロポリタン山形2F
											</address>
											<a href="#" class="googlemap">
												<span class="pc">Google Map</span>
												<img class="sp" src="../images/common/icon_gmap.svg" alt="">
											</a>
										</div>
										<table>
											<tr>
												<th>TEL.</th>
												<td>023‐679‐5929</td>
											</tr>
											<tr>
												<th>営業時間</th>
												<td>11:00〜21:00（L.O 20:00）</td>
											</tr>
										</table>
										<div class="cate-list">
											<span class="cate">とんかつ</span>
											<span class="cate">豚肉料理</span>
											<span class="cate">GotoEat対象店舗</span>
											<span class="cate">GoToトラベル地域共通クーポン取扱店舗</span>
										</div>
									</div>
								</li>
								<li>
									<div class="img-wrap">
										<div class="img" style="background: url(../images/store/img01.jpg)center center/cover no-repeat;"></div>
										<div class="cate-list">
											<span class="label brw">飲食店</span>
											<span class="label grn">物販店</span>
										</div>
									</div>
									<div class="text">
										<strong>とんかつと豚肉料理   平田牧場 ホテルメトロポリタン山形店</strong>
										<div class="address">
											<address>
												山形県山形市香澄町1-1-1 ホテルメトロポリタン山形2F
											</address>
											<a href="#" class="googlemap">
												<span class="pc">Google Map</span>
												<img class="sp" src="../images/common/icon_gmap.svg" alt="">
											</a>
										</div>
										<table>
											<tr>
												<th>TEL.</th>
												<td>023‐679‐5929</td>
											</tr>
											<tr>
												<th>営業時間</th>
												<td>11:00〜21:00（L.O 20:00）</td>
											</tr>
										</table>
										<div class="cate-list">
											<span class="cate">とんかつ</span>
											<span class="cate">豚肉料理</span>
											<span class="cate">GotoEat対象店舗</span>
											<span class="cate">GoToトラベル地域共通クーポン取扱店舗</span>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</section>
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
