<?php
$pagename = str_replace(array('/', '.php', '?s='), '', $_SERVER['REQUEST_URI']);
$pagename = $pagename ? $pagename : 'default';
$pagename = str_replace('', '', $pagename);


switch ($pagename) {
	case 'shop':
		if(!isset($seo_title)) $seo_title = '直営店舗一覧 | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場は山形県、宮城県、東京都に店舗を展開しています。物販店と飲食店が併設している店舗やそれぞれに特化した店舗もあるので、お近くの店舗をお探しください。また、店舗独自でイベントやキャンペーンを行っている場合もありますので、ぜひ各店舗の詳細ページもご覧ください。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'about':
		if(!isset($seo_title)) $seo_title = '平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '1964年の創業以来、私たちが歩んできた道のりや、企業情報などをご紹介いたします。日本で、いや、世界でいちばんの丁寧をめざそう。いのちに感謝する日本の食のDNAを受け継いで、いちばん丁寧にいのちと向き合うブランドになろう。これまでも、これからも。平田牧場の変わらない想いです。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
  case 'anniversary60th':
		if(!isset($seo_title)) $seo_title = '平田牧場60周年記念 | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場は1964年に創業し2024年に創業60周年を迎えました。「いのち」に丁寧に向き合い、効率を優先せず、きちんと手間をかけ、おいしくて安全・安心・健康な豚肉を皆様にお届けし、社会全体も健康にするよう、私たちは努力して参ります。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'abouthistory':
		if(!isset($seo_title)) $seo_title = '平田牧場のあゆみ | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = 'すべては、たった2頭の豚からはじまった。平田牧場が歩んできた道のりについて、ご紹介しています。年表で平田牧場の歴史をたどっていただけます。平田牧場はこれからも、皆様の健康とともにあり続け、進化し続けます。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'aboutbrand':
		if(!isset($seo_title)) $seo_title = 'ブランド豚のご紹介 | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = 'たった2頭の豚から始まった平田牧場の豚づくり。今では年間約20万頭を出荷できるまでになりました。平田牧場の最高級ブランド豚「平田牧場金華豚」、平田牧場の代名詞とも言える「平田牧場三元豚」。それぞれの特徴についてご紹介します。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'aboutbrandkinkaton':
		if(!isset($seo_title)) $seo_title = '平田牧場 金華豚 | ブランド豚のご紹介 | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場金華豚は「幻の豚」と呼ばれる、世界でも希少な最高品種です。中国浙江省金華地区原産で、世界三大ハムの一つに数えられる高級中華食材「金華ハム」の原料豚として知られています。その優れた肉質を損なうことなく改良した平田牧場では純血種の「平田牧場純粋金華豚」（純粋種）と、優れた肉質を実現しながら生産効率を高めた「平田牧場金華豚」（交配種）の2種類の金華豚を生産しています。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'aboutbrandsangenton':
		if(!isset($seo_title)) $seo_title = '平田牧場 三元豚 | ブランド豚のご紹介 | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場三元豚は、3つの品種の豚を掛け合わせた「三元交配豚」です。特に肉質を重視した系統選抜を行い、品種系統の明らかな3種類の豚を掛け合わせることで、最高の肉質を実現することに成功しました。肉のきめが細かく、心地よい歯ごたえのお肉に仕上がります。真っ白で甘く、舌先でとろける上質な脂肪は、プロの料理人からも高い評価をいただいています。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'aboutgroup':
		if(!isset($seo_title)) $seo_title = '平田牧場グループ | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場のグループ企業、直営店舗の一覧をご紹介いたします。多くの事業所や生産の現場があって平田牧場は成り立っています。今後も一丸となって山形をはじめ東北地方を盛り上げていきたいと思っております。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'aboutcommitment':
		if(!isset($seo_title)) $seo_title = 'コミットメント | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場グループは、より豊かな食生活・食文化を提案する「健康創造企業」として、おいしく健康に良い食材の生産やお客様に高いご満足を提供できるよう、力を尽くしてまいります。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'aboutcommitmenthealth':
		if(!isset($seo_title)) $seo_title = '無添加へのこだわり | コミットメント | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '「健康に育った豚肉だけを原料にした、安心して食べられるハム・ソーセージが欲しい…」。1970年代当時、届けられたお客様からの声にお応えするために、私たちは無着色、無添加のハム・ソーセージの開発を手探りでスタートしました。以来、一貫してこだわり続けてきたことは、「健康な豚を育てること」と「安心して食べられる食品をつくり続けること」です。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'aboutcommitmentsdgs':
		if(!isset($seo_title)) $seo_title = 'SDGsへの取り組み | コミットメント | 平田牧場について | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場は持続可能な開発目標【SDGs】を支援してます。より良い社会を実現するために。そして、安心して食べられる美味しい豚肉を、これからもつくり続けていくために。平田牧場は、地域に密着して、「飼料用米プロジェクト」や「有機畜産」などさまざまな社会貢献活動を進めSDGsを支援していきます。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	case 'news':
		if(!isset($seo_title)) $seo_title = 'ニュース | 平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場からの各種ニュースやキャンペーン、各店舗のイベント情報などを掲載しています。';
		if(!isset($seo_keyword)) $seo_keyword = '';
	break;
	default:
		if(!isset($seo_title)) $seo_title = '平田牧場公式サイト';
		if(!isset($seo_description)) $seo_description = '平田牧場は、健康創造企業です。豚肉の生産から加工・流通・販売に至るまで自社で行っています。その製品づくりの基本は「おいしく・安全・安心」。つねに未来に向かって新しい取り組みを実行しながら、平田牧場 金華豚・三元豚の美味しさをお届けします。';
		if(!isset($seo_keyword)) $seo_keyword = '';
}
?>
