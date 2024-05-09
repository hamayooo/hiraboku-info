<?php
session_start();
include_once(dirname(__DIR__) . '/app_config.php');
if(!empty($_SESSION['statusFlag'])) unset($_SESSION['statusFlag']);
else header('location: '.APP_URL);
$thisPageName = 'contact';
include(APP_PATH."libs/head.php");

unset($_SESSION['ses_gtime_step2']);
unset($_SESSION['ses_from_step2']);
unset($_SESSION['ses_step3']);
?>
<meta http-equiv="refresh" content="15; url=<?php echo APP_URL ?>">
<script type="text/javascript">
history.pushState({ page: 1 }, "title 1", "#noback");
window.onhashchange = function (event) {
	window.location.hash = "#noback";
};
</script>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/contact.min.css">
</head>
<body id="contact" class="indexThx">
	<!-------------------------------------------------------------------------
	HEADER
	--------------------------------------------------------------------------->
	<?php include(APP_PATH."libs/header.php") ?>
	<div class="mainImg"><h2>CONTACT<span>お問い合わせ</span></h2></div>
	<div class="container clearfix">
		
		<div class="stepImg">
			<img src="<?php echo APP_ASSETS; ?>img/common/form/img_step03.svg" width="714" height="45" alt="フォームからのお問い合わせ　Step" class="pc" />
			<img src="<?php echo APP_ASSETS; ?>img/common/form/img_step03SP.svg" width="345" height="55" alt="フォームからのお問い合わせ　Step" class="sp" />
		</div>

		<div class="containerIndexThx">
			<p class="t20b20 fz18"><strong>送信が完了いたしました。</strong></p>
			<p class="mt20 t20b20 fz14">
				お問い合わせありがとうございます。<br>後日、担当よりご連絡させていただきます。<br />3日以内に弊社より連絡がない場合はお手数ですが、再送信もしくは直接ご連絡くださいますようお願い致します。<br>
			</p>
			<p class="t20b0"><a href="<?php echo APP_URL;?>">←TOPへ戻る</a></p>
		</div>
	</div>
	<?php // include(APP_PATH.'libs/contactBox.php') ?>
	<!-------------------------------------------------------------------------
	FOOTER
	--------------------------------------------------------------------------->
	<?php include(APP_PATH.'libs/footer.php') ?>
	</body>
</html>
