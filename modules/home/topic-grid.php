<?php
if(!function_exists('topic_feed')) {
    function topic_feed() {
        $topics = gpm('topics');
        $lt_image = gpm('last_tile_image');
        $lt_image = wp_get_attachment_url($lt_image, 'full');
        ?>
        <section class="topic_feed">
            <div class="container container-xlg flex row afs">
                <div class="item_1_10">TOPICS</div>
                <div class="item_9_10">
                    <div class="content flex row afc jfs">
                        <?php 
                        foreach($topics as $topic) {
                            $bg_image = get_term_meta($topic, 'topic_image', true);
                            $bg_image = wp_get_attachment_url($bg_image, 'full');
                            $topic_obj = get_term_by( 'id', absint( $topic ), 'topic' );
                            $topic_title = $topic_obj->name;
                            $topic_slug = $topic_obj->slug;

                            echo '<div class="topic item_1_4"><div class="topic-wrap flex afc"><div class="inner-wrap flex afc"><img src="'.$bg_image.'"/><a href="/topic/'.$topic_slug.'" class="topic-title flex afc jfc">'.$topic_title.'</a></div></div></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function($){
                    function checkVisible(elm, eval) {
                        eval = eval || "object visible";
                        var viewportHeight = $(window).height(), // Viewport Height
                            scrolltop = $(window).scrollTop(), // Scroll Top
                            y = $(elm).offset().top,
                            elementHeight = $(elm).height();

                        if (eval == "object visible") return ((y < (viewportHeight + scrolltop)) && (y > (scrolltop - elementHeight)));
                        if (eval == "above") return ((y < (viewportHeight + scrolltop)));
                    }
                    $(window).on('scroll', function() {
                        if (checkVisible($('.topic_feed'))) {
                            console.log('visible');
                            $('.topic_feed .item_1_10').css('top', '45px');
                        }
                    });
                });
            </script>
        </section>
        <?php
    }
}