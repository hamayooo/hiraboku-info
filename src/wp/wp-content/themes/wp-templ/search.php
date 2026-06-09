<?php
$s = $_GET['s'];
$get_cats = $_GET['get_cats'];


if($get_cats) {
	$tax_ary[] = array(
		'taxonomy' => 'category',
		'field' => 'slug',
		'terms' => $get_cats,
		'operator' => 'IN', //ANDかIN
	);
}

?>
<?php if (!($s || $get_cats || $get_tags)): ?>
	<p>検索条件を指定してください</p>
	<?php get_search_form(); ?>
<?php else: ?>

<h1>検索結果</h1>
<?php
if(is_array($get_cats)) {
	echo '<p>カテゴリー:';
	foreach ($get_cats as $val) {
		$p_term = get_term_by('slug', $val, 'category');
		echo $p_term->name;
		if ($val !== end($get_cats)) {
			echo ', ';
		}
	}
	echo '</p>';
} ?>


<?php
	$my_query = new WP_Query( array(
		'paged' => get_query_var('paged'),
		'post_type' => 'store',
		'tax_query' => $tax_ary,
		'relation' => 'AND', //ANDかOR
		's' => $s,
)); ?>
<?php if($my_query->have_posts() ) : ?>
<ul>
<?php while( $my_query->have_posts()) : $my_query->the_post(); ?>
<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
</li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>結果が見つかりませんでした。</p>
<?php endif; ?>
<?php get_search_form(); ?>
<?php endif; ?>
