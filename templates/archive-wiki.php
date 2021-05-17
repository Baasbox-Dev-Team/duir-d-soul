<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */



get_header();

$results = Bb_Wiki_Public::getPaginatedWikiByLetters(get_query_var(('page')));

?>

<h1 class="display-1">
	<?php the_title(); ?>
</h1>

<div class="masonry">
    <?php foreach($results as $letter => $words): ?>
    <div class="letter-block">
        <h3><?php echo($letter); ?></h3>
        <ul>
            <?php foreach($words as $word): ?>
            <li><a href="<?php echo rtrim(get_permalink($word["id"]), "/"); ?>"><?php echo $word["title"]; ?></a></li>
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
}

.letter-block { /* Masonry bricks or child elements */
  display: inline-block;
  margin: 0 0 1em;
  width: 100%;
}
</style>

<?php get_footer(); ?>
