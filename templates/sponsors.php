<?php
// Template name: Sponsors

get_header();
?>
<div class="sponsors gtw">
    <div class="container container-xsm">
        <?php 
        if(have_posts()): while(have_posts()): the_post();
            echo '<h1 class="page-title default-title">'.get_the_title().'</h1>';
            echo '<div class="sponsors-content">'.get_the_content().'</div>';
        endwhile; endif;
        ?>
    </div>
    <div class="container container-md-lg">
    <?php
    $args = array(
        'post_type' => 'sponsor',
        'status' => 'publish',
        'posts_per_page' => -1,
    );

    $sponsors = new WP_Query($args);
    if($sponsors->found_posts) {
        echo '<div class="flex row afc jfc">';
        foreach($sponsors->posts as $sponsor) {
            $sponsorID = $sponsor->ID;
            $sponsor_image = get_the_post_thumbnail_url($sponsorID, 'sponsor-size');
            $link = get_post_meta($sponsorID, 'outbound_link', true);
            if(filter_var($link, FILTER_VALIDATE_URL)) {
                $link = 'href="'.$link.'" ';
            }else {
                $link = "";
            }
            echo '<div class="sponsor item_1_3 flex row afc jfc">';
            echo '<a '.$link.'target="_blank">';
            echo '<img src="'.$sponsor_image.'" />';
            echo '</a>';
            echo '</div>';
        }
    }
    ?>
    </div>
</div>
<?php
get_footer();
?>