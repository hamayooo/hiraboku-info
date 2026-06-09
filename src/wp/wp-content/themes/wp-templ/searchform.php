<?php
  $args = array(
    'post_type' => 'store',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC'
  );
  if(!empty($_POST['search_category'])) {
    foreach($_POST['search_category'] as $value) {
      $search_category[] = htmlspecialchars($value, ENT_QUOTES);
    }
    $args += array('category__in' => $search_category);
  }
?>

<div class="tab-content tab-content03">
  <form role="search" method="get" id="searchform" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="input-list">
      <ul>
        <li>
          <input type="checkbox" name="get_cats[]" value="tonkatsu" id="tonkatsu">
          <label for="tonkatsu">
            <i></i>
            <span>とんかつ</span>
          </label>
        </li>
        <li>
          <input type="checkbox" name="get_cats[]" value="tonkatsu2" id="tonkatsu2">
          <label for="tonkatsu2">
            <i></i>
            <span>とんかつ</span>
          </label>
        </li>
        <li>
          <input type="checkbox" name="get_cats[]" value="pork-dish" id="pork-dish">
          <label for="pork-dish">
            <i></i>
            <span>豚肉料理</span>
          </label>
        </li>
      </ul>
      <div class="btn-box">
        <a href="#" class="btn clear">クリア</a>
        <button type="submit" class="btn entry-btn" value="検索する" name="submit">
          検索する
        </button>
      </div>
    </div>
  </form>
</div>
