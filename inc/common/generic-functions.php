<?php
// custom wrapper function for 'get_post_meta' in content rows
function gpm($key, $wysiwyg = false, $post_id = false){
    global $post;
    $post_id = $post_id ? $post_id : $post->ID;

    if($wysiwyg){
        return apply_filters('the_content', get_post_meta($post_id, $key, true));
    }else{
        return get_post_meta($post_id, $key, true);
    }
}

// custom excerpt length
function get_excerpt($postID=false, $numletters){
	if($postID) {
		$content_post = get_post($postID);
		$content = $content_post->post_content;
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		$excerpt = $content;
	}else {
		$excerpt = get_the_content();
	}
	
	$excerpt = preg_replace(" ([*?])",'',$excerpt);
	$excerpt = strip_shortcodes($excerpt);
	$excerpt = strip_tags($excerpt);
	$excerpt = substr($excerpt, 0, $numletters);
	$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
	// $excerpt = trim(preg_replace( '/s+/', ' ', $excerpt));
	$excerpt = $excerpt.'...';
	return $excerpt;
}