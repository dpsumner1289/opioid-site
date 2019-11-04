<?php 
if(!function_exists('related_posts')) {
    function related_posts() {
        $postID = get_the_ID();
        $topics = wp_get_post_terms($postID, 'topic');
        $types = wp_get_post_terms($postID, 'resource_type');
        $all_terms = array_merge($topics, $types);
        function cmp($a, $b) {
            return strcmp($a->name, $b->name);
        }
        usort($all_terms, "cmp");
        ?>
        <section class="related-posts">
            <h2>RELATED ARTICLES IN...</h2>
            <div class="article-listing flex row afs jfs">
                <?php
                foreach($all_terms as $term) {
                    $args = array(
                        'post_type'=>'resource',
                        'post_status'=>'publish',
                        'posts_per_page'=>3,
                        'post__not_in' => array($postID),
                        'tax_query' => array(array(
                            'taxonomy' => $term->taxonomy,
                            'field' => 'term_id',
                            'terms' => $term->term_id,
                        )),
                    );
                    $rposts = new WP_Query($args);
                    if($rposts->found_posts) {
                        $output = '<div class="article-term item_1_2 flex col afs jfs">';
                        $output.= '<h3>'.strtoupper($term->name).'</h3>';
                        foreach($rposts->posts as $rpost) {
                            $output .= '<a class="hidden-arrow" href="'.get_the_permalink($rpost->ID).'">'.get_the_title($rpost->ID).'<i class="fas fa-arrow-right"></i></a>';
                        }
                        $output.= '</div>';
                    }else {
                        $output = '';
                    }
                    echo $output;
                }
                ?>
            </div>
        </section>
        <?php
    }
}