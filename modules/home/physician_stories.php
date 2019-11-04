<?php
if(!function_exists('physician_stories')) {
    function physician_stories() {
        $feed_type = gpm('feed_type');
        $story_select = gpm('story_select');
        $number_of_stories = gpm('number_of_stories');
        ?>
        <section class="physician_stories">
            <div class="container container-xlg flex row afs">
                <div class="item_1_10">MEMBER STORIES</div>
                <div class="item_9_10">
                    <div class="content flex col afs jfc">
                        <?php
                        $args = array(
                            'post_status' => 'publish',
                            'post_type' => 'physician_story',
                        );
                        if($feed_type == 'manual') {
                            $args['post__in'] = $story_select;
                        }elseif($feed_type == 'auto') {
                            $args['posts_per_page'] = $number_of_stories;
                        }
                        $stories = new WP_Query($args);
                        if($stories->found_posts) {
                            $s = 0;
                            foreach($stories->posts as $story) {
                                $sID = $story->ID;
                                $s++;
                                $doc_pic = get_the_post_thumbnail($sID, 'full');
                                $excerpt = get_excerpt($sID, 170);
                                $link = get_the_permalink($sID);
                                ?>
                                <div class="story flex row afc <?php echo $s%2 == 0 ? 'even' : 'odd'; ?>">
                                    <div class="image item_1_3 flex afc"><?php echo $doc_pic; ?></div>
                                    <div class="text item_2_3 flex col afs jfs">
                                        <div class="excerpt">
                                            <span class="huge-quote">“</span>
                                            <?php echo $excerpt; ?>
                                        </div>
                                        <a class="readmore" href="<?php echo $link; ?>">READ THE STORY <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                                <?php
                            }
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
                        if (checkVisible($('.physician_stories'))) {
                            console.log('visible');
                            $('.physician_stories .item_1_10').css('top', '116px');
                            $(window).off('scroll');
                        }
                    });
                });
            </script>
        </section>
        <?php
    }
}