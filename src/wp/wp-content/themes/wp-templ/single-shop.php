<?php
$path = realpath(dirname(__FILE__) . '') . "/../../../../";
include_once $path . 'app_config.php';

$seo_title = get_field('title');
$seo_description = get_field('description');

include $path . 'libs/meta.php';

?>
</head>

<body class="store_detail">



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
				<span><?php echo get_the_title(); ?></span>
			</h1>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span><a href="<?php echo APP_URL;?>">HOME</a></span>
					</li>
					<li>
						<span><a href="<?php echo APP_URL;?>shop/">店舗一覧</a></span>
					</li>
					<li>
						<span><?php echo get_the_title(); ?></span>
					</li>
				</ul>
			</div>
		</div>




		<section class="content">
			<div class="inner">
				<div class="content__top">
					<div class="store-slider">
						<?php
							$img01 = get_field('top')['img01'];
							$img02 = get_field('top')['img02'];
							$img03 = get_field('top')['img03'];
						?>
						<?php if($img01):?>
						<div class="slide">
							<div class="img" style="background: url(<?php echo $img01; ?>)center center/cover no-repeat;" data-img="<?php echo $img01; ?>"></div>
						</div>
						<?php endif;?>
						<?php if($img02):?>
						<div class="slide">
							<div class="img" style="background: url(<?php echo $img02; ?>)center center/cover no-repeat;" data-img="<?php echo $img02; ?>"></div>
						</div>
						<?php endif;?>
						<?php if($img03):?>
						<div class="slide">
							<div class="img" style="background: url(<?php echo $img03; ?>)center center/cover no-repeat;" data-img="<?php echo $img03; ?>"></div>
						</div>
						<?php endif;?>
					</div>
					<div class="dots pc"></div>
					<div class="store-slider-thum sp">
						<?php if($img01):?>
						<div class="slide" style="background: url(<?php echo $img01; ?>)center center/cover no-repeat;" data-img="<?php echo $img01; ?>"></div>
						<?php endif;?>
						<?php if($img02):?>
						<div class="slide" style="background: url(<?php echo $img02; ?>)center center/cover no-repeat;" data-img="<?php echo $img02; ?>"></div>
						<?php endif;?>
						<?php if($img03):?>
						<div class="slide" style="background: url(<?php echo $img03; ?>)center center/cover no-repeat;" data-img="<?php echo $img03; ?>"></div>
						<?php endif;?>
					</div>
					<div class="text">
						<p>
							<?php echo get_field('top')['text']; ?>
						</p>
					</div>
				</div>
				<div class="content__bottom">
					<div class="text">
						<div class="cate-list">
							<?php
								$terms = get_the_terms($post->ID,'storecat');
								foreach( $terms as $term ):
								$term_name = $term->name;
								$color = ColorfulCategories::getColorForTerm($term->term_id, true);
							?>
							<span class="label" style="background-color:<?php echo $color;?>;"><?php echo $term_name;?></span>
							<?php endforeach;?>
						</div>
						<table>
							<tr>
								<th>住所</th>
								<td><?php echo get_field('info')['address']; ?></td>
							</tr>
							<tr>
								<th>TEL.</th>
								<td><?php echo get_field('info')['tel']; ?></td>
							</tr>
							<tr>
								<th>営業時間</th>
								<td><?php echo get_field('info')['time']; ?></td>
							</tr>
						</table>
						<?php $labels = get_field('info')['label'];?>
						<?php if($labels):?>
						<div class="cate-list2">
							<?php foreach ($labels as $label) :?>
							<span class="label"><?php echo $label['text']; ?></span>
							<?php endforeach;?>
						</div>
						<?php endif;?>

						<?php $lists = get_field('info')['list'];?>
						<?php if($lists):?>
						<ul class="link-list pc">
							<?php foreach ($lists as $list) :?>
							<li>
								<a href="<?php echo $list['link']; ?>"><?php echo $list['text']; ?></a>
							</li>
							<?php endforeach;?>
						</ul>
						<?php endif;?>
					</div>
					<div class="map">
						<?php echo get_field('info')['googlemap']; ?>
					</div>
					<?php $lists = get_field('info')['list'];?>
					<?php if($lists):?>
					<ul class="link-list sp">
						<?php foreach ($lists as $list) :?>
						<li>
							<a href="<?php echo $list['link']; ?>"><?php echo $list['text']; ?></a>
						</li>
						<?php endforeach;?>
					</ul>
					<?php endif;?>
				</div>
			</div>
		</section>


		<?php
			$thisId = get_the_id();
      $sNews = new WP_Query(array(
      'post_type' => 'news',
      'posts_per_page' => 6,
      'meta_query' => array(
            array(
                'key'     => 'store',
                'value'   => '"' . $thisId . '"' ,
                'compare' => 'LIKE',
            ),
        )
      ));
      // if ($sNews->have_posts()) : $sNews->the_post();
      // $customer = get_field('customer');
			if ( $sNews->have_posts() ):
    ?>
		<section class="news">
			<div class="inner">
				<div class="news__box">
					<h3>店舗ニュース</h3>
					<ul class="news-list">
						<?php while($sNews->have_posts()): $sNews->the_post();?>
						<?php
							$thumbnail_id = get_post_thumbnail_id();
							$eye_img = wp_get_attachment_image_src( $thumbnail_id , 'full' );
							$bg_img = $eye_img[0];
						?>
						<li>
							<a href="<?php echo get_permalink(); ?>">
								<div class="img" style="background: url(<?php echo $bg_img;?>)center center/cover no-repeat;"></div>
								<div class="date-cate">
									<div class="date">
										<small><?php the_time('Y'); ?>.</small><?php the_time('m.d'); ?>
									</div>
									<?php
										if ($terms = get_the_terms($post->ID, 'newscat')) :
										foreach ( $terms as $term ) :
										$term_name = $term->name;
										$color = ColorfulCategories::getColorForTerm($term->term_id, true);
									?>
									<span class="cate olv" style="background-color:<?php echo esc_attr($color); ?>;"><?php echo $term_name; ?></span>
									<?php endforeach; endif;?>
								</div>
								<p class="title"><?php echo get_the_title(); ?></p>
							</a>
						</li>
						<?php endwhile; ?>
					</ul>
					<a href="<?php echo APP_URL;?>news/" class="more">もっとみる</a>
				</div>
			</div>
		</section>
		<?php endif; wp_reset_postdata(); ?>



		<div class="prev">
			<a href="<?php echo APP_URL;?>shop/">店舗一覧へ戻る</a>
		</div>






	</main>




	<!-- Footer
	======================================================================-->
	<?php include $path . 'libs/footer.php';?>



</div>



<!-- Scripts
======================================================================-->
<?php include $path . 'libs/scripts.php';?>
</body>
</html>
