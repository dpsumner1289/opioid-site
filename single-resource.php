<?php
get_header();
$hero_image = get_post_meta($post->ID, 'hero_image', true);
$hero_image = wp_get_attachment_url($hero_image);
$citations = get_post_meta($post->ID, 'citations', true);
$content_upload = get_post_meta($post->ID, 'content_upload', true);
$linked_content = get_post_meta($post->ID, 'linked_content', true);
$content_upload = wp_get_attachment_url($content_upload);
?>
<div class="color-layer gtw">
    <div class="image-layer" <?php if($hero_image){echo 'style="background-image:url('.$hero_image.');"';} ?>>
    <div id="resource-content" class="container container-md-lg">
        <div class="container container-md no-padding align-left">
            <div class="inner-header-wrap">
                <?php echo '<h1 class="post-title">'.$post->post_title.'</h1>'; ?>
                <i class="post-date">Published <?php echo date('F dS, Y', strtotime($post->post_date)); ?></i>
            </div>
        </div>
        <div class="inner-content-wrap flex row afs jfs">
            <div class="item_7_10">
                <div class="content"><?php echo apply_filters('the_content', $post->post_content); ?></div>
                <?php if($citations > 0): ?>
                <div class="citations flex row afs">
                    <div class="item_1_10">CITATIONS</div>
                    <div class="item_9_10 flex col afs jfs">
                        <?php
                        for($i = 0; $i < $citations; $i++) {
                            $ckey = 'citations_'.$i.'_';
                            $citation = get_post_meta($post->ID, $ckey.'citation', true);
                            $citation = apply_filters('the_content', $citation);
                            echo '<div class="citation">'.$citation.'</div>';
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(!empty($content_upload)){
                    echo '<a class="content-dl button gtb" href="'.$content_upload.'" target="_blank">Download Content</a>';
                } ?>
                <?php if(!empty($linked_content)){
                    echo '<a class="content-dl button gtb" href="'.$linked_content.'" target="_blank">View Content</a>';
                } ?>
                <?php related_posts(); ?>
            </div>
            <div class="sidebar item_3_10">
                <div class="inner-sidebar-wrap flex col afc jfc">
                    <div class="share-topics flex col">
                        <div class="share flex row afc jfs"><span>SHARE</span><?php echo do_shortcode('[Sassy_Social_Share]'); ?></div>
                        <div class="topics flex col afs jfs">
                            <h4>Topics</h4>
                            <?php
                            $topics = get_the_terms($post->ID, 'topic');
                            foreach($topics as $topic) {
                                echo '<div class="topic-listing flex row afc jfsb"><a class="topic-name hidden-arrow" href="/topic/'.$topic->slug.'">'.$topic->name.'<i class="fal fa-arrow-right"></i></a><a class="subscribe" href="#" data-tax="topic" data-term="'.$topic->term_id.'"><i class="fal fa-plus"></i> SUBSCRIBE TO THIS TOPIC</a></div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="sidebar-search flex row full">
                        <form class="hero-search">
                            <div class="search-field">
                                <i class="fas fa-search"></i>
                                <input class="keywords" type="text" placeholder="SEARCH RESOURCES" />
                                <button class="post-search" type="submit"><i class="fas fa-arrow-right"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="tax-tiles flex row full">
                        <?php 
                        $args = array(
                            'taxonomy' => 'topic',
                            'hide_empty' => false,
                        );
                        $all_topics = get_terms($args);
                        foreach($all_topics as $topic) {
                            $bg_image = get_term_meta($topic->term_id, 'topic_image', true);
                            $bg_image = wp_get_attachment_url($bg_image, 'full');
                            $topic_title = $topic->name;
                            $topic_slug = $topic->slug;

                            echo '<div class="topic flex row full afc jfc" style="background-image:url('.$bg_image.');"><a href="/topic/'.$topic_slug.'" class="topic-title hidden-arrow flex afc jfc">'.$topic_title.'<i class="fas fa-arrow-right"></i></a></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            // build the Query
            function Query(tax, term, keywords="") {
                this.tax = tax;
                this.term = term;
                this.keywords = keywords;
            }
            // subscribe to current query
            function subscribe(query) {
                var dataSave = {
                    action : 'otf_subscribe_to_this',
                    query : query,
                }
                $.post("<?php echo admin_url('admin-ajax.php'); ?>", dataSave, function(response){
                    $('#resource-content').append(response);
                    $('.save-confirmation.successful').fadeOut(1500);
                    $('.save-confirmation.exists').fadeOut(2700);
                });
            }

            // subscribe to current query
            $(document).on('click', 'a.subscribe:not([data-type="search"])', function(e){
                e.preventDefault();
                subQuery = new Query($(this).data('tax'), $(this).data('term'), "");
                subscribe(subQuery);
            });
            $(document).on('focusin', '.search-field input.keywords', function(e){
                $(this).parent('.search-field').css('border-color', '#ACBD49');
            });
            $(document).on('focusout', '.search-field input.keywords', function(e){
                $(this).parent('.search-field').css('border-color', '#6D6E71');
            });
        });
    </script>
</div>
<?php
get_footer();