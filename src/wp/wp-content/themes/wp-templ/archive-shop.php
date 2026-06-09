<?php
$path = realpath(dirname(__FILE__) . '') . "/../../../../";
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
			<h1>
				<strong>店舗一覧</strong>
				<small>Store Information</small>
			</h1>
		</div>


		<div class="pankuzu">
			<div class="inner">
				<ul>
					<li>
						<span><a href="<?php echo APP_URL;?>">HOME</a></span>
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
						<li class="active" data-triger="0">エリア<br class="sp">で探す</li>
						<li data-triger="1">目的別<br class="sp">に探す</li>
						<li data-triger="2">カテゴリー<br class="sp">で探す</li>
					</ul>
				</div>
				<div class="search__content">
					<div class="tab-content is-active">
						<div class="list">
							<?php
								$parent_terms = get_terms('areacat', array('hide_empty' => false, 'parent' => 0));
								foreach ( $parent_terms as $pt ) :
					      $pt_id = $pt->term_id;
					      $pt_name = $pt->name;
					      $pt_url = get_term_link($pt);
							?>
							<div class="list__box">
								<strong><?php echo $pt_name; ?></strong>
								<ul>
									<?php
										$child_terms = get_terms( 'areacat', array('hide_empty' => false, 'parent' => $pt_id) );
										foreach ( $child_terms as $ct ) :
			              $ct_id = $ct->term_id;
			              $ct_name = $ct->name;
			              $ct_url = get_term_link($ct);
									?>
									<li>
										<a href="<?php echo $ct_url; ?>#search-text"><?php echo $ct_name; ?></a>
									</li>
									<?php endforeach; ?>
								</ul>
							</div>
							<?php endforeach; ?>

						</div>
					</div>
					<div class="tab-content tab-content02">
						<div class="list">
							<?php
								$terms = get_terms('storecat');
								foreach ($terms as $key => $term):
								$term_name = $term->name;
								$link = get_term_link($term);
							?>
							<div class="list__box">
								<ul>
									<li>
										<a href="<?php echo $link;?>#search-text"><?php echo $term_name;?></a>
									</li>
								</ul>
							</div>
							<?php endforeach;?>
						</div>
					</div>


					<div class="tab-content tab-content03">
						<form role="search" method="get" id="searchform" action="<?php echo esc_url(home_url('/')); ?>shop/">
						<!-- <form class="" action="<?php //echo esc_url(home_url() . $_SERVER['REQUEST_URI']); ?>" method="post"> -->
							<div class="input-list">
								<ul>
									<?php
										$terms = get_terms('categorycat');
										foreach ($terms as $key => $term):
										$term_name = $term->name;
										$term_slug = $term->slug;
										$link = get_term_link($term);
									?>
									<li>
										<input type="checkbox" name="get_cats[]" value="<?php echo $term_slug;?>" id="<?php echo $term_slug;?>">
										<label for="<?php echo $term_slug;?>">
											<i></i>
											<span><?php echo $term_name;?></span>
										</label>
									</li>
									<?php endforeach;?>
								</ul>
								<div class="btn-box">
									<a href="#" class="btn clear" id="reset">クリア</a>
									<button type="submit" class="btn entry-btn" value="検索する" name="submit">
										検索する
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>




<?php
	// $searchArray = ['relation'=>'AND'];
	// if(isset($_GET['get_cats']) || isset($_GET['get_othercat'])):
	// 		$array = array(
	// 			'taxonomy' => 'categorycat',
	// 			'field' => 'slug',
	// 			'terms' => $_GET['get_cats'],
	// 			'operator' => 'IN'
	// 		);
	// 		array_push($searchArray, $array);
	// endif;

?>






<?php if(!isset($_GET['get_cats'])): ?>

		<div class="content">
			<?php
				$get_parent_cats = array(
					'parent' => '0', //get top level categories only
					'taxonomy' => 'areacat',
					'hide_empty' => 0
				);
				$args=array(
					'post_type' => 'shop',
					'posts_per_page'=> -1,
				);
				$query = new WP_Query( $args );
				$terms = get_categories( $get_parent_cats );
				$term_slug = $section->slug;
				$slug = str_replace('-', ' ', $term_slug);
				$term_name = $section->name;
			?>

			<?php
				$all_categories = get_categories( $get_parent_cats );
				foreach( $all_categories as $single_category ):
				$catID = $single_category->cat_ID;
			?>
			<?php
				$single_category_name = html_entity_decode($single_category->name);
			?>
			<section class="area-box">
				<div class="inner">
					<h3><?php echo $single_category_name; ?></h3>

					<?php
						$get_children_cats = array(
								'child_of' => $catID,
								'taxonomy' => 'areacat'
						);
						$child_cats = get_categories( $get_children_cats );
						foreach( $child_cats as $child_cat ):
						$childID = $child_cat->cat_ID;
					?>

					<?php
						$term_slug = $child_cat->slug;
						$slug = str_replace('-', ' ', $term_slug);
						$term_name = $child_cat->name;
						$term_id = $childID;
					?>
					<div class="area-box__box">
						<div class="area01">
							<span><?php echo $term_name ?></span>
						</div>
						<div class="detail">
							<?php
								$query = new WP_Query( array(
									'posts_per_page'=>-1,
									'post_type'=>'shop',
									'tax_query' => array(
											array(
												'taxonomy' => 'areacat',
												'field'    => 'id',
												'terms'    => $childID,
											),
										),
								) );
							?>
							<ul class="detail__list">
								<?php
									while( $query->have_posts() ):$query->the_post();
								?>
								<?php
									$thumbnail_id = get_post_thumbnail_id();
									$eye_img = wp_get_attachment_image_src( $thumbnail_id , 'full' );
									$bg_img = $eye_img[0];
								?>
								<li>
									<div class="img-wrap">
										<div class="img" style="background: url(<?php echo $bg_img;?>)center center/cover no-repeat;"></div>
										<div class="cate-list">
											<?php
												if ($terms = get_the_terms($post->ID, 'storecat')) :
												foreach ( $terms as $term ) :
												$term_name = $term->name;
												$color = ColorfulCategories::getColorForTerm($term->term_id, true);
												$link = get_term_link($term);
											?>
											<a class="label" style="background-color:<?php echo esc_attr($color); ?>;" href="<?php echo $link;?>#search-text"><?php echo $term_name; ?></a>
											<?php endforeach; endif;?>
										</div>
									</div>
									<div class="text">
										<strong>
											<a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a>
										</strong>
										<div class="address">
											<address>
												<?php echo get_field('info')['address']; ?>
											</address>
											<a href="<?php echo get_field('info')['googlemap_url']; ?>" class="googlemap" target="_blank">
												<span class="pc">Google Map</span>
												<img class="sp" src="../images/common/icon_gmap.svg" alt="">
											</a>
										</div>
										<table>
											<tr>
												<th>TEL.</th>
												<td><?php echo get_field('info')['tel']; ?></td>
											</tr>
											<tr>
												<th>営業時間</th>
												<td><?php echo get_field('info')['time']; ?></td>
											</tr>
										</table>

										<div class="cate-list">
											<?php
												if ($terms = get_the_terms($post->ID, 'categorycat')) :
												foreach ( $terms as $term ) :
												$term_name = $term->name;
												$link = get_term_link($term);
											?>
											<a class="cate" href="<?php echo $link;?>#search-text"><?php echo $term_name; ?></a>
											<?php endforeach; endif;?>
										</div>

									</div>
								</li>
								<?php endwhile; ?>
							</ul>

						</div>
					</div>
					<?php endforeach;?>


				</div>
			</section>
			<?php endforeach;?>

		</div>




	<?php else: ?>
	<!-- 検索する場合 -->
		<div class="content" id="search-text">
			<div class="inner">
				<div class="search-text">
					<?php

						if($_GET['get_cats']):
							$catsArray = $_GET['get_cats'];
							foreach($catsArray as &$term){
								$termName = get_term_by('slug', $term, 'categorycat');
								$term =$termName->name;
							}

					?>
					<em>「<?php echo implode('」「',$catsArray)?>」</em>で検索中です。
					<?php endif ?>
				</div>
			</div>
		</div>

		<div class="content">
			<?php
				$args=array(
					'post_type' => 'shop',
					'posts_per_page'=> -1,
					'tax_query' => array(
            array(
              'taxonomy' => 'categorycat',
              'field' => 'slug',
              'terms' => $_GET['get_cats'],
							'operator' => 'IN'
            ),
						// $searchArray
          ),
				);
				$query = new WP_Query( $args );

			?>

			<?php
				$prevParentCat = "";
				$prevCat = "";
				$postCount = 0;
				if($query->have_posts()): while($query->have_posts()): $query->the_post();
				$areaCat = get_the_terms($post->ID, 'areacat');
				$parentCat = $areaCat[0]->name;
				$childCat = $areaCat[1]->name;
			?>
			<?php
				if($parentCat != $prevParentCat):
					if($postCount != 0):
			?>
		</div>
	</section>
		<?php endif ?>

			<section class="area-box">
				<div class="inner">

					<h3><?php echo $parentCat ?></h3>
				<?php endif ?>

				<?php

				if ($childCat != $prevChildCat):
					if($parentCat == $prevParentCat):
				?>
				</ul>

			</div>
		</div>
	<?php endif ?>
					<div class="area-box__box">
						<div class="area01">
							<span><?php echo $childCat ?></span>
						</div>
						<div class="detail">
							<ul class="detail__list">
								<?php
									endif;
								?>
								<?php
									$thumbnail_id = get_post_thumbnail_id();
									$eye_img = wp_get_attachment_image_src( $thumbnail_id , 'full' );
									$bg_img = $eye_img[0];
								?>
								<li>
									<div class="img-wrap">
										<div class="img" style="background: url(<?php echo $bg_img;?>)center center/cover no-repeat;"></div>
										<div class="cate-list">
											<?php
												if ($terms = get_the_terms($post->ID, 'storecat')) :
												foreach ( $terms as $term ) :
												$term_name = $term->name;
												$color = ColorfulCategories::getColorForTerm($term->term_id, true);
												$link = get_term_link($term);
											?>
											<a class="label" style="background-color:<?php echo esc_attr($color); ?>;" href="<?php echo $link;?>#search-text"><?php echo $term_name; ?></a>
											<?php endforeach; endif;?>
										</div>
									</div>
									<div class="text">
										<strong>
											<a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a>
										</strong>
										<div class="address">
											<address>
												<?php echo get_field('info')['address']; ?>
											</address>
											<a href="<?php echo get_field('info')['googlemap_url']; ?>" class="googlemap" target="_blank">
												<span class="pc">Google Map</span>
												<img class="sp" src="../images/common/icon_gmap.svg" alt="">
											</a>
										</div>
										<table>
											<tr>
												<th>TEL.</th>
												<td><?php echo get_field('info')['tel']; ?></td>
											</tr>
											<tr>
												<th>営業時間</th>
												<td><?php echo get_field('info')['time']; ?></td>
											</tr>
										</table>

										<div class="cate-list">
											<?php
												if ($terms = get_the_terms($post->ID, 'categorycat')) :
												foreach ( $terms as $term ) :
												$term_name = $term->name;
												$link = get_term_link($term);
											?>
											<a class="cate" href="<?php echo $link;?>#search-text"><?php echo $term_name; ?></a>
											<?php endforeach; endif;?>
											</div>

</div>
</li>
<?php //endwhile; ?>
<?php if($query->current_post + 1 == $query->post_count): ?>
</ul>

</div>
</div>

</div>
</section>
<?php endif ?>
<?php
$prevParentCat = $parentCat;
$prevChildCat = $childCat;
$postCount++;
endwhile; else: ?>

<!-- 検索結果なし -->
<div class="result">
	<div class="inner">
		<p>検索結果が見つかりませんでした。</p>
	</div>
</div>

 <?php endif;
	?>

		</div>


	<?php endif ?>








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
