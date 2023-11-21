<?php 
    $title = get_the_title(get_the_ID());
    $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    $permalink = get_the_permalink(get_the_ID());
?>

<div class="guesty_property_grid">
    <img src="<?= $featured_image; ?>" alt="<?= $title; ?>" class="guesty_property_grid_image">
    <a href="<?= $permalink; ?>"><h3 class="guesty_property_grid_name"><?= $title; ?></h3></a>
</div>