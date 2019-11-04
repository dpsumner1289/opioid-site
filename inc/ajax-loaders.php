<?php
// resource filtering (resources.php)
add_action("wp_ajax_resource_filter", "resource_filter");
add_action("wp_ajax_nopriv_resource_filter", "resource_filter");
function resource_filter() {
	$ID = get_the_ID();
	ob_start();
    $page = $_POST['page'];
    $keywords = $_POST['keywords'];
    $r_queries = $_POST['query'];
	$args = array(
		'paged' => $page,
		'post_type' => 'resource',
		'post_status' => 'publish',
        'posts_per_page' => 12,
        's' => $keywords,
        'meta_query' => array(
            array(
                'key' => 'hide_from_search',
                'value' => 0,
            )
        )
    );
    
    if($r_queries) {
        $tax_query = [];
        $tax_query['relation'] = 'AND';
        $topic_terms = [];
        $type_terms = [];
        foreach($r_queries as $rq) {
            $tax = $rq['tax'];
            $term = $rq['term'];
            if($tax === 'topic') {
                array_push($topic_terms, $term);
            }elseif($tax === 'resource_type') {
                array_push($type_terms, $term);
            }
        }
        if(!empty($topic_terms)) {
            $tax_query_1 = array(
                'taxonomy'=>'topic',
                'field' => 'id',
                'terms'=>$topic_terms,
                'operator' => 'IN'
            );
            array_push($tax_query, $tax_query_1);
        }
        if(!empty($type_terms)) {
            $tax_query_2 = array(
                'taxonomy'=>'resource_type',
                'field' => 'id',
                'terms'=>$type_terms,
                'operator' => 'IN'
            );
            array_push($tax_query, $tax_query_2);
        }
        $args['tax_query'] = $tax_query;
    }
	$resources = new WP_Query($args);

	if($resources->found_posts) {
		foreach($resources->posts as $resource){
			$thisID = $resource->ID;
			$title = get_the_title($thisID);
            $terms = get_the_terms($thisID, 'resource_type');
            $topics = get_the_terms($thisID, 'topic');
            $image = get_the_post_thumbnail($thisID, 'resource-thumb');
            if(!$image) {
                $image = get_option('options_default_resource_image_listing_page', true);
                $image = wp_get_attachment_url($image);
                $image = '<img src="'.$image.'"/>';
            }
            $date = get_the_date('n/j/Y', $thisID);
            $link = get_the_permalink($thisID);
			?>
			<div class="resource item_1_4">
				<div class="resource_wrapper">
					<div class="resource_inner_wrapper flex col afs jfs">
                        <div class="resource_image flex row afc">
                            <a class="image-link" href="<?php echo $link; ?>">
                                <?php echo $image; ?>
                                <span class="date"><?php echo $date; ?></span>
                            </a>
                        </div>
                        <div class="resource_title">
                            <a class="title-link" href="<?php echo $link; ?>"><h3><?php echo $title; ?></h3></a>
                        </div>
                        <div class="resource_taxes flex full row afs">
                            <div class="topics item_1_2 nobreak"><div class="tax-wrap">Topic: 
                            <?php foreach($topics as $topic) {
                                $topic_slug = $topic->slug;
                                echo '<a href="/topic/'.$topic_slug.'" data-tax="topic" data-term="'.$topic->term_id.'">'.$topic->name.'</a> ';
                            } ?></div>
                            </div>
                            <div class="types item_1_2 nobreak"><div class="tax-wrap">Type: 
                            <?php foreach($terms as $term) {
                                $term_slug = $term->slug;
                                if($term->parent != 0):
                                echo '<a href="/resource_type/'.$term_slug.'" data-tax="resource_type" data-term="'.$term->term_id.'">'.$term->name.'</a>';
                                endif;
                            } ?></div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
			<?php
        }
        ?>
        <script>
        jQuery(document).ready(function($) {
            <?php if($resources->max_num_pages > 1 && $page < $resources->max_num_pages): ?>
            var moreposts = '<a href="#" name="moreposts" class="moreposts button wgtgw">Read More</a>';
            <?php else: ?>
            var moreposts = '';
            <?php endif; ?>
            $('.resource_feed_html').append(moreposts);
        });
        </script>
        <?php
	} else {
		echo '<h1 style="width:100%; text-align:center;margin-bottom:1.618rem;">SORRY, NOTHING WAS FOUND.</h1>';
		echo '<h3 style="width:100%; text-align:center;margin-bottom:3.326rem;">TRY ANOTHER SEARCH?</h3>';
	}
	echo ob_get_clean();
	die();
}

// search saving
add_action("wp_ajax_otf_save_search", "otf_save_search");
add_action("wp_ajax_nopriv_otf_save_search", "otf_save_search");
function otf_save_search() {
    global $current_user;
    $r_queries = $_POST['query'];
    $postsnum = $_POST['postsnum'];
    $save_me = array(
        'queries' => $r_queries, 
        'posts_num' => $postsnum,
        'date_saved' => date('n/j/y'),
    );
    $encoded_save = json_encode($save_me);
    $user_id = $current_user->ID;
    $meta = get_user_meta( $user_id, 'saved_search' );
    add_user_meta($user_id, 'saved_search', $encoded_save);

    echo '<div class="save-confirmation flex row afc jfc">Saved</div>';
    die();
}

// subscribe to topics
add_action("wp_ajax_otf_subscribe_to_this", "otf_subscribe_to_this");
add_action("wp_ajax_nopriv_otf_subscribe_to_this", "otf_subscribe_to_this");
function otf_subscribe_to_this() {
    global $current_user;
    $r_queries = $_POST['query'];
    $postsnum = $_POST['postsnum'];
    $save_me = array(
        'tax' => $r_queries['tax'],
        'term' => $r_queries['term'], 
        'date_saved' => date('n/j/y'),
    );
    $encoded_save = json_encode($save_me);
    $user_id = $current_user->ID;
    $meta = get_user_meta($user_id, 'otf_subscription', false);
    $decoded_meta = [];
    $save_me = (object) $save_me;
    foreach($meta as $m) {
        $this_m = json_decode($m);
        array_push($decoded_meta, $this_m);
    }
    if(!in_array($save_me, $decoded_meta)) {
        add_user_meta($user_id, 'otf_subscription', $encoded_save);
        echo '<div class="save-confirmation successful flex row afc jfc">Subscribed!</div>';
    }else {
        echo '<div class="save-confirmation exists flex row afc jfc">Subscription already exists!</div>';
    }

    die();
}

// retrieve saved searches
add_action("wp_ajax_otf_retrieve_searches", "otf_retrieve_searches");
add_action("wp_ajax_nopriv_otf_retrieve_searches", "otf_retrieve_searches");
function otf_retrieve_searches() {
    ?>
    <div class="searches-wrapper flex col">
        <h3>Your Saved Searches</h3>
        <?php 
        global $wpdb;
        $user_id = $_POST['userID'];
        $sql = "SELECT * FROM {$wpdb->prefix}usermeta WHERE user_id = '{$user_id}' AND meta_key = 'saved_search'";
        $results = $wpdb->get_results($sql);
        foreach($results as $row) {
            $umeta = $row->umeta_id;
            $this_user = $row->user_id;
            $mtype = $row->meta_key;
            $mvalue = $row->meta_value;
            $saved = json_decode($mvalue);
            $search_queries = $saved->queries;
            $date_saved = $saved->date_saved;
            foreach($search_queries as $squery) {
                $keywords = $squery->keywords;
                if(!empty($keywords)) {
                    echo '<div class="saved-search flex row afc jfsb">';
                    echo '<div class="phrase"><a href="#" class="searchit" data-searchthis="'.$keywords.'">"'.$keywords.'"</div>';
                    echo '<div class="date">Saved '.$date_saved.'</div>';
                    echo '<a href="#" class="delete-search" data-keywords="'.$keywords.'" data-umeta="'.$umeta.'"><i class="fal fa-times"></i></a>';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
    <?php
    die();
}

// retrieve subscriptions
add_action("wp_ajax_otf_retrieve_subscriptions", "otf_retrieve_subscriptions");
add_action("wp_ajax_nopriv_otf_retrieve_subscriptions", "otf_retrieve_subscriptions");
function otf_retrieve_subscriptions() {
    ?>
    <div class="searches-wrapper flex col">
        <h3>Your Monthly Topic Subscriptions</h3>
        <?php 
        global $wpdb;
        $user_id = $_POST['userID'];
        $sql = "SELECT * FROM {$wpdb->prefix}usermeta WHERE user_id = '{$user_id}' AND meta_key = 'otf_subscription'";
        $results = $wpdb->get_results($sql);
        foreach($results as $row) {
            $umeta = $row->umeta_id;
            $this_user = $row->user_id;
            $mtype = $row->meta_key;
            $mvalue = $row->meta_value;
            $saved = json_decode($mvalue);
            $topic = get_term($saved->term, $saved->tax)->name;
            $slug = get_term($saved->term, $saved->tax)->slug;
            $date_saved = $saved->date_saved;
            echo '<div class="saved-search flex row afc jfsb">';
            echo '<div class="phrase"><a href="/topic/'.$slug.'">'.$topic.'</a></div>';
            echo '<div class="date">Saved '.$date_saved.'</div>';
            echo '<a href="#" class="delete-subscription" data-umeta="'.$umeta.'"><i class="fal fa-times"></i></a>';
            echo '</div>';
        }
        ?>
    </div>
    <?php
    die();
}

// delete saved searches
add_action("wp_ajax_otf_delete_searches", "otf_delete_searches");
add_action("wp_ajax_nopriv_otf_delete_searches", "otf_delete_searches");
function otf_delete_searches() {
    global $current_user;
    global $wpdb;
    $user_id = $current_user->ID;
    $umeta = $_POST['umeta'];
    $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}usermeta` WHERE umeta_id = %d AND user_id = %s", $umeta, $user_id));
    echo '<div class="save-confirmation flex row afc jfc">Search Deleted!</div>';
    die();
}

// delete subscriptions
add_action("wp_ajax_otf_delete_subscriptions", "otf_delete_subscriptions");
add_action("wp_ajax_nopriv_otf_delete_subscriptions", "otf_delete_subscriptions");
function otf_delete_subscriptions() {
    global $current_user;
    global $wpdb;
    $user_id = $current_user->ID;
    $umeta = $_POST['umeta'];
    $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}usermeta` WHERE umeta_id = %d AND user_id = %s", $umeta, $user_id));
    echo '<div class="save-confirmation flex row afc jfc">Subscription Deleted!</div>';
    die();
}

// delete all data
add_action("wp_ajax_otf_delete_all", "otf_delete_all");
add_action("wp_ajax_nopriv_otf_delete_all", "otf_delete_all");
function otf_delete_all() {
    global $current_user;
    $user_id = $current_user->ID;
    delete_user_meta($user_id, 'saved_search');
    delete_user_meta($user_id, 'otf_subscription');
    echo '<div class="save-confirmation flex row afc jfc">All Data Deleted!</div>';
    die();
}