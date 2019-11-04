<?php // Template name: Playbook
get_header();
$postID = get_the_ID();
$hero_image = get_post_meta($postID, 'hero_image', true);
$hero_image = wp_get_attachment_url($hero_image);
$heading_small_portion = get_post_meta($postID, 'heading_small_portion', true);
$heading_large_portion = get_post_meta($postID, 'heading_large_portion', true);
$introduction = get_post_meta($postID, 'introduction', true);
?>
<section class="playbook_hero" <?php if($hero_image){echo 'style="background-image:url('.$hero_image.');"';} ?>>
    <div class="color_layer gtw-t">
        <div class="container container-xsm flex col afc jfc">
            <h1>
                <div class="small"><?php echo $heading_small_portion; ?></div>
                <div class="large"><?php echo $heading_large_portion; ?></div>
            </h1>
            <?php if($introduction){
                echo '<div class="intro">'.$introduction.'</div>';
            } ?>
        </div>
    </div>
</section>
<section class="chapters">
    <div class="container container-1240 flex col jfc">
        <h2>TABLE OF CONTENTS</h2>
        <?php
        $args = array(
            'taxonomy' => 'playbook_chapter',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        );
        $chapters = get_terms($args);
        foreach($chapters as $chapter) {
            ?>
            <div class="chapter flex row afs jfc">
            <?php
            $chapter_image = get_term_meta($chapter->term_id, 'chapter_image', true);
            $chapter_image = wp_get_attachment_url($chapter_image);
            $chapter_image = '<img src="'.$chapter_image.'"/>';
            echo '<div class="chapter-image item_1_3">'.$chapter_image.'</div>';
            $cargs = array(
                'post_status' => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy'=>'playbook_chapter',
                        'terms'=> $chapter->term_id,
                    )
                ),
            );
            $chapter_posts = new WP_Query($cargs);
            if($chapter_posts->found_posts) {
                echo '<div class="chapter-posts item_2_3">';
                echo '<h3>'.$chapter->name.'</h3>';
                echo '<ul>';
                foreach($chapter_posts->posts as $cpost) {
                    echo '<li><i class="fad fa-angle-right"></i><a href="'.get_the_permalink($cpost->ID).'">'.get_the_title($cpost->ID).'</a></li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            ?>
            </div>
            <?php
        }
        ?>
    </div>
</section>
<?php
// global_cta();
get_footer();