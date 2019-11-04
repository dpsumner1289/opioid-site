<?php
get_header();
if(have_posts()): while(have_posts()): the_post();
?>
<div class="main-wrapper gtw">
    <div class="container container-md">
        <?php
        echo '<h1 class="page-title default-title">'.get_the_title().'</h1>';
        echo '<div class="blog-content">';
        the_content();
        echo '</div>';
        ?>
    </div>
</div>
<?php
endwhile; endif;
get_footer();
?>