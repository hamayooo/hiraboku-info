<?php
$path = realpath(dirname(__FILE__) . '') . "/../../../";
include_once $path . 'app_config.php';
include $path . 'libs/meta.php';

?>
</head>

<body class="sbgs">



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
				<strong>SDGsへの取り組み</strong>
				<small>Approach to SDGs</small>
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
						<span><a href="<?php echo APP_URL;?>about/commitment/">コミットメント</a></span>
					</li>
					<li>
						<span>SDGsへの取り組み</span>
					</li>
				</ul>
			</div>
		</div>

		<div id="wide-visual">
			<figure class="pc" style="background-image:url(<?php echo APP_URL; ?>images/about/commitment/sdgs/mv.jpg);"></figure>
			<figure class="sp" style="background-image:url(<?php echo APP_URL; ?>images/about/commitment/sdgs/mv-sp.jpg);"></figure>
		</div>

        <section class="sab">
          <div class="inner">
            <h2>個人だけでなく、社会も健康にしたい</h2>
            <figure class="sdgs-main"><img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/logo.png" class=""></figure>
            <h3>平田牧場は持続可能な開発目標<br class="sp">【SDGs】を支援してます。</h3>
            <p class="caption">
                より良い社会を実現するために。<br class="pc">
                そして、安心して食べられる美味しい豚肉を、これからもつくり続けていくために。<br>
                平田牧場は、地域に密着して、「飼料用米プロジェクト」や「有機畜産」など<br class="pc">
                さまざまな社会貢献活動を進めSDGsを支援していきます。
            </p>
          </div>
          <div class="sdgs">
            <div class="cont">
              <h2 class="title"><span>日本の米育ち 平田牧場金華豚・三元豚は、</span>持続可能な取り組みで育てる<br>サステイナブルポーク。</h2>
              <figure>
                  <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/sdgs_sub_01.png">
              </figure>
            </div>
            <div class="text-cont">
              <p>
              地球温暖化や人口爆発により人類は<br class="pc">
              危機的な状況を迎えることが予測され、<br class="pc">
              世界でSDGs2030へ向けて 様々な取り組みが急がれています。<br class="pc">
              平田牧場でも長年実施している飼料用米を中心に、<br class="pc">
              子どもたちの未来と、持続可能な社会の実現に向け、<br class="pc">
              取り組みを加速させます。
              </p>
              <figure>
                  <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/sdgs_sub_02.png">
              </figure>
              <h3>持続可能な社会の実現に向けて</h3>
              <div class="line-cont">
                <p>減反田、休耕田を活用し、適地適作である米を飼料として給餌します。</p>
                <p>国産の飼料用米は、日本の農業や水田文化、環境を守り、食料安全保障につながります。</p>
                <p>健康に育った豚の糞は質の高い堆肥となり土地に還元、ここで飼料用米を作ることにより他国の食糧や水を奪わない資源循環に取り組んでいます。</p>
                <p>再生可能エネルギーによる発電を積極的に活用し工場を稼働させます。</p>
                <figure>
                <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/sdgs_main.png" class="pc">
                <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/sdgs_main_sp.png" class="sp">
                </figure>
              </div>
            </div>
          </div>
        </section>
        <section class="main">
            <div class="inner">
                <div class="cont">
                    <h2 class="title">飼料用米プロジェクト</h2>
                    <div class="item">
                        <figure>
                            <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/img01.svg" class="pc">
                            <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/img01-sp.svg" class="sp">
                        </figure>
                        <div class="text-cont">
                            <h2>国内自給率を高め、<br>日本の農業を活性化するために</h2>
                            <p>
                                私たちは、国内の自給力を高め、日本の農業を活性化するために、環境保全型農業の試みである「飼料用米プロジェクト」に取り組んでいます。<br>
                                循環型の国内自給の向上をめざしたこのプロジェクトでは、リサイクルや有機農業を取り入れた環境保全型の畜産経営で、安定的な生産と流通を目指しています。
                            </p>
                        </div>
                    </div>
                    <div class="item">
                        <figure>
                            <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/img02.jpg" class="">
                            <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/img03.jpg" class="img03">
                        </figure>
                        <div class="text-cont">
                            <h2>輸入食料に依存する、日本の食生活</h2>
                            <p>
                            日本の食料自給率は40%あまり※1。食肉用の家畜の穀物飼料では、ほとんど自給できていません。あえて「食」を「命」と言い換えるなら、私たちの「命」はすでに海外の食料に依存しているというのが現状です。一方で日本の水田は、米の消費の減少による減反政策や農業従事者の高齢化などにより、その35パーセントが休耕田や転作田になっています。荒れた遊休農地は害虫の繁殖などにより地域の環境を悪化させますが、水田だった土地では他の作物をつくることが難しく、たとえつくっても価格面で外国の穀物とは競争になりません。そのため農業は少しずつ衰退し、米の消費が横ばいになっても自給率はさらに低下を続けているのです。
                            </p>
                            <p class="detail">※1  <a href="https://www.maff.go.jp/j/zyukyu/zikyu_ritu/011_2.html" target="_blank">https://www.maff.go.jp/j/zyukyu/zikyu_ritu/011_2.html</a></p>
                        </div>
                    </div>
                </div>
                <div class="data-cont">
                    <h2>飼料用米で、<br class="sp">美味しさと食料自給力の向上を両立</h2>
                    <p>私たちは山形県庄内地域で、休耕田を利用し飼料用米を作ってそれを豚の飼料にする環境保全型「飼料用米プロジェクト」を進めています。生協からの提案で遊佐町と平田牧場が協同で2004年に取り組みを開始、これまでに着実に成果を上げ、日本各地から視察団も訪れています。日本一の取組規模を誇る平田牧場では、2016年には作付面積1,885haを実現し、11,333tもの飼料用米を集荷。一頭あたりの飼料用米の給餌量は、プロジェクト開始当初の19kgから、73.5kgにまで増加しています。また、粉砕した飼料用米を混ぜた飼料を食べて育った豚の脂身には、オレイン酸が多く含まれていて甘みと旨味があり、リノール酸が少ないことから脂の酸化を抑制する効果もあると言います。餌に米を混ぜるというのは、昔から「落ち穂拾いをした鴨は美味しい」と言われていたことから発想したもので、遊佐町の生産者グループは食用より粒の大きい「べこあおば」（現在は「ふくひびき」が中心）という品種を選んで直播きし、この米を食べる豚の尿を醗酵させた液肥を利用するという環境保全型農業に取り組み、量産とコストダウンを図ってきました。</p>
                    <figure><img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/img04.png" class=""></figure>
                    <p class="detail">飼料用米は（写真左から）直播き、豚尿液肥栽培、NPOによる農地借受けなどによって低コストと量産を実現しています。</p>
                    <figure>
                        <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/img05.jpg" class="pc">
                        <img src="<?php echo APP_URL; ?>images/about/commitment/sdgs/img05-sp.jpg" class="sp">
                        </figure>
                    <h2>日本の未来を支えるプロジェクトとして</h2>
                    <p>飼料用米プロジェクトを支えているのは、生産者だけではありません。飼料用米は価格が安く補助金なしでは生産が成り立ちにくいため、遊佐町は国から食料自給率向上特区の認定を得て、NPO法人も生産に参加できるようにしました。その結果、当初24人だった生産者は現在200人を超えました。しかし平田牧場のすべての豚に飼料用米を与えるには、遊佐町の生産だけでは足りないため、今後は産地の拡大が必要になります。そこにまた補助金の問題が出てきます。現在は平田牧場側が費用を負担することで農家が安心して飼料用米を生産できる価格を維持していますが、それでは根本的な解決になりません。本当の意味で自給力を高めるには、生産者だけでなく、行政や消費者の理解と協力が必要です。自給率が120パーセントといわれる山形県に住む私たちは、国内自給を高める活動のリーダーとして課題を一つずつ解決し、全国に示すという責任を課せられていると考えています。子どもたちに確かな未来と、持続可能な社会を手渡していくために。一人でも多くの方に、このプロジェクトの意義と、そこから生まれた豚肉の価値を評価していただけるよう、私たちはこれからも努力と工夫を重ねていきます。</p>
                </div>


								<div class="flow">
									<h3>飼料用米の生産から、食卓までの流れ</h3>
									<ul class="flow__list">
										<li>
											<div class="img">
												<img class="flow-img01" src="../../../images/about/commitment/sdgs/flow01.png" alt="">
											</div>
											<span class="grn">1, 飼料用米の生産</span>
										</li>
										<li>
											<div class="img">
												<img class="flow-img02" src="../../../images/about/commitment/sdgs/flow02.png" alt="">
											</div>
											<span class="grn">2, JA(RC)調整・保管</span>
										</li>
										<li>
											<div class="img">
												<img class="flow-img03" src="../../../images/about/commitment/sdgs/flow03.png" alt="">
											</div>
											<span class="grn">3, 飼料会社</span>
										</li>
										<li>
											<div class="img">
												<img class="flow-img04" src="../../../images/about/commitment/sdgs/flow04.png" alt="">
											</div>
											<span class="grn">4, 平田牧場（農場）</span>
										</li>
										<li>
											<div class="img">
												<img class="flow-img05" src="../../../images/about/commitment/sdgs/flow05.png" alt="">
											</div>
											<span class="grn">5, 平田牧場（加工）</span>
										</li>
										<li>
											<div class="img">
												<img class="flow-img06" src="../../../images/about/commitment/sdgs/flow06.png" alt="">
											</div>
											<span class="grn">6, 食卓</span>
										</li>
									</ul>
								</div>

            </div>
        </section>
        <a href="../health/" class="wide-ban"　target="_blank">無添加へのこだわり</a>
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
