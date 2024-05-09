<?php
$path = realpath(dirname(__FILE__) . '') . "/../../../../";
include_once $path . 'app_config.php';
$the_year = get_query_var('year');
$the_month = get_query_var('monthnum');

$seo_title = is_date() ? $the_year . '年' . $the_month . '月' . ' | ニュース | 平田牧場公式サイト': null;
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
			<h1>
				<strong>ニュース</strong>
				<small>News</small>
			</h1>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span><a href="<?php echo APP_URL;?>">HOME</a></span>
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
					<?php if ( have_posts() ): ?>
					<ul class="news-list">
						<?php while(have_posts()): the_post();?>
						<?php
							$thumbnail_id = get_post_thumbnail_id();
							$eye_img = wp_get_attachment_image_src( $thumbnail_id , 'full' );
							$bg_img = $eye_img[0];
						?>
						<li>
							<a href="<?php the_permalink(); ?>">
								<div class="img" style="background: url(<?php echo $bg_img;?>)center center/cover no-repeat;"></div>
								<div class="text">
									<div class="date-cate">
										<div class="date">
											<small><?php the_time('Y'); ?>.</small><?php the_time('m.d'); ?>
										</div>
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
									</div>
									<p class="title"><?php the_title(); ?></p>
								</div>
							</a>
						</li>
						<?php endwhile; ?>
					</ul>

					<nav class="pagination">
						<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(array('query' => $wp_query)); } ?>
						<!-- <div class="wp-pagenavi">
							<a href="#" class="page prev"></a>
							<span class="page current">1</span>
							<a href="#" class="page larger">2</a>
							<a href="#" class="page next"></a>
						</div> -->
					</nav>

					<?php endif; wp_reset_postdata(); ?>
				</div>
				<div class="sidebar">
					<div class="sidebar-wrap">
						<div class="box">
							<strong>カテゴリー</strong>
							<ul>
								<li>
									<a href="<?php echo APP_URL;?>news/">すべて</a>
								</li>
								<?php
	  							$terms = get_terms('newscat');
	  							if (have_posts() ):
	  						?>
								<?php
									foreach ($terms as $key => $term):
									$term_name = $term->name;
									$link = get_term_link($term);
								?>
								<li>
									<a href="<?php echo $link;?>"><?php echo $term_name;?></a>
								</li>
								<?php endforeach;?>
								<?php endif; ?>
							</ul>
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
