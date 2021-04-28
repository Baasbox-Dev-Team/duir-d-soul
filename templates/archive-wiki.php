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

// query for your post type
$query  = new WP_Query([ 
    'post_type'      => 'wiki',  
    'posts_per_page' => -1 ,
    'no_found_rows'  => true,
    'orderby' => 'title', 
    'order' => 'DESC'
]);

if ($query->posts) 
{
    foreach ( $query->posts as $key => $post ) {
        $first_letter = substr($post->post_title,0,1);

        if(!empty($first_letter)) {
            $results[$first_letter][] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
            );
        }
    }
}

if(!empty($results)) {
    ksort( $results );
}

?>

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
    <?php endforeach; ?>
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
