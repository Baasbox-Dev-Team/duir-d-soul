<?php
$results = Bb_Wiki_Public::getPaginatedWikiByLetters(get_query_var(('page')));
?>

<div class="masonry">
    <?php foreach($results as $letter => $words): ?>
    <div class="letter-block">
        <h3><?php echo($letter); ?></h3>
        <ul>
            <?php foreach($words as $word): ?>
            <li><a title="<?php echo $word["title"]; ?>" href="<?php echo rtrim(get_permalink($word["id"]), "/"); ?>"><?php echo $word["title"]; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php 
        endforeach; 
        wp_reset_postdata();
        wp_reset_query();
    ?>

</div>

<style>
.masonry { /* Masonry container */
    column-count: 4;
    column-gap: 1em;
    max-width: 100% !important;
}

.letter-block { /* Masonry bricks or child elements */
  display: inline-block;
  margin: 0 0 1em;
  width: 100%;
}

@media (max-width: 1024px) {
    .masonry {
        column-count: 2;
    }
}


</style>

