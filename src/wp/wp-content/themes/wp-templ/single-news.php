<?php
$path = realpath(dirname(__FILE__) . '') . "/../../../../";
include_once $path . 'app_config.php';

$seo_title = get_field('title');
$seo_description = get_field('description');

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
			<h1>
				<span class="pc">ニュース / News</span>
				<strong class="sp">ニュース</strong>
				<small class="sp">News</small>
			</h1>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span><a href="<?php echo APP_URL;?>">HOME</a></span>
					</li>
					<li>
						<span><a href="<?php echo APP_URL;?>news/">ニュース</a></span>
					</li>
					<li>
						<span><?php echo get_the_title(); ?></span>
					</li>
				</ul>
			</div>
		</div>



		<div class="content">
			<div class="inner">


				<div class="left">
					<div class="date-cate">
						<div class="date">
							<small><?php the_time('Y'); ?>.</small><?php the_time('m.d') ?>
						</div>
						<div class="cate">
							<?php
								$terms = get_the_terms($post->ID,'newscat');
								if($terms):
								foreach( $terms as $term ):
								$term_name = $term->name;
								$color = ColorfulCategories::getColorForTerm($term->term_id, true);
							?>
							<span class="label" style="background-color:<?php echo $color;?>;"><?php echo $term_name;?></span>
							<?php endforeach;?>
							<?php endif;?>
						</div>
					</div>
					<h3 class="title"><?php echo get_the_title(); ?></h3>
					<div class="contents">
					<?php
            $this_content= wpautop($post->post_content);
            echo ($this_content);
          ?>
					</div>


					<?php
						$prev_post = get_previous_post();
						$next_post = get_next_post();
					?>
					<div class="pagenavi">
						<?php
							if (!empty($prev_post)):
						?>
						<a href="<?php echo get_permalink($prev_post->ID); ?>" class="prev">
							<strong>前の記事</strong>
							<p><?php echo get_the_title($prev_post->ID); ?></p>
						</a>
					 <?php endif;?>

						<a href="<?php echo APP_URL;?>news/" class="all"><strong>ニュース一覽へ</strong></a>

						<?php
							if (!empty($next_post)):
						?>
						<a href="<?php echo get_permalink($next_post->ID); ?>" class="next">
							<strong>次の記事</strong>
							<p><?php echo get_the_title($next_post->ID); ?></p>
						</a>
						<?php endif;?>
					</div>
				</div>


				<div class="sidebar">
					<div class="sidebar-wrap">
						<div class="box">
							<strong>カテゴリー</strong>
							<?php
								$terms = get_terms('newscat');
								if (have_posts() ):
							?>
							<ul>
								<li>
									<a href="<?php echo APP_URL;?>news/">すべて</a>
								</li>
								<?php
									foreach ($terms as $key => $term):
									$term_name = $term->name;
									$link = get_term_link($term);
								?>
								<li>
									<a href="<?php echo $link;?>"><?php echo $term_name;?></a>
								</li>
								<?php endforeach;?>
							</ul>
							<?php endif; ?>
						</div>
						<div class="box box02">
							<strong>アーカイブ</strong>
							<ul>
								<?php
					        global $wpdb;
					        $limit = 0;
					        $current_post_type = get_post_type();
					        $year_prev = null;
					        $months = $wpdb->get_results("SELECT DISTINCT MONTH( post_date ) AS month ,	YEAR( post_date ) AS year, COUNT( id ) as post_count FROM $wpdb->posts WHERE post_status = 'publish' and post_date <= now( ) and post_type = '$current_post_type' GROUP BY month , year ORDER BY post_date DESC");

					        foreach($months as $month) :
					        $year_current = $month->year;

					        if ($year_current != $year_prev)
					        {
					          if($year_current != date('Y'))
					          {
					      ?>
					      </ul>
					      <?php
					          }
					      ?>
								<li>
									<span class="trigger"><?php echo $month->year; ?></span>
									<ul>
										<?php
					            }
					          ?>
										<li>
											<a href="<?php echo APP_URL; ?><?php echo $month->year; ?>/<?php echo date("m", mktime(0, 0, 0, $month->month, 1, $month->year))."/?post_type=".$current_post_type; ?>">-<?php echo $month->month;?>月</a>
										</li>
										<?php
					            $year_prev = $year_current;
					            endforeach;
					           ?>
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
