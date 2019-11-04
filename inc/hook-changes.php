<?php
// content submission form

// add post data to radio fields
add_filter( 'gform_field_content', 'add_custom_attr', 10, 2);
function add_custom_attr($field_content, $field){
    if ( $field['type'] === 'radio' && strpos( $field['cssClass'], 'pop-types' ) != false ) {
        $field_content = str_replace( 'type=', "data-status='test' type=", $field_content );
    }
    return $field_content;
}

// dynamically populate topics
add_filter( 'gform_pre_render_1', 'populate_topics' );
add_filter( 'gform_pre_validation_1', 'populate_topics' );
add_filter( 'gform_pre_submission_filter_1', 'populate_topics' );
add_filter( 'gform_admin_pre_render_1', 'populate_topics' );
function populate_topics( $form ) {
    foreach ( $form['fields'] as &$field ) {
        if ( $field['type'] != 'checkbox' || strpos( $field['cssClass'], 'pop-topics' ) === false ) {
            continue;
        }
        $terms = get_terms(array('taxonomy'=>'topic', 'hide_empty'=>false));
        $choices = array();
        foreach ( $terms as $term ) {
            $choices[] = array( 'value' => $term->name, 'text' => $term->name );
        }
        $field['choices'] = $choices;
 
    }
    return $form;
}

// dynamically populate types
add_filter( 'gform_pre_render_1', 'populate_types' );
add_filter( 'gform_pre_validation_1', 'populate_types' );
add_filter( 'gform_pre_submission_filter_1', 'populate_types' );
add_filter( 'gform_admin_pre_render_1', 'populate_types' );
function populate_types( $form ) {
    foreach ( $form['fields'] as &$field ) {
        if ( $field['type'] != 'radio' || strpos( $field['cssClass'], 'pop-types' ) === false ) {
            continue;
        }
        $terms = get_terms(array('taxonomy'=>'resource_type', 'hide_empty'=>false));
        $choices = array();
        foreach ( $terms as $term ) {
            if($term->parent !== 0) {
                $choices[] = array( 'value' => $term->name, 'text' => $term->name );
            }
        }
        $field['choices'] = $choices;
 
    }
    return $form;
}

add_filter( 'gform_pre_render_1', 'submission_detail' );
function submission_detail( $form ) {
    $current_page = GFFormDisplay::get_current_page( $form['id'] );
    $html_content = "<div class='preview flex col'>";
    if ( $current_page == 4 ) {
        foreach ( $form['fields'] as &$field ) {
            if ( $field->id != 31 && $field->type != 'page' ) {
                switch($field->id) {
                    case 29:
                        $field_data = rgpost('input_' . $field->id );
                        $html_content .= '<h2>'.$field_data.'</h2>';
                        break;
                    case 30:
                        $field_data = rgpost('input_' . $field->id );
                        $html_content .= '<p>'.$field_data.'</p>';
                        break;
                    case 28:
                        $field_data = rgpost('input_' . $field->id );
                        if(!empty($field_data)) {
                            $html_content .= '<div class="media flex row afc jfs"><h4>MEDIA</h4>';
                            $html_content .= '<p>'.$field_data.'</p>';
                            $html_content .= '</div>';
                        }
                }
            }
        }
        $html_content .= "</div>";
        foreach( $form['fields'] as &$field ) {
            if ( $field->id == 31 ) {
                $field->content = $html_content;
            }
        }
    }
    return $form;
}



// Gravity Forms File Upload Attachment ID 

/**
 * Save file upload fields under custom post field to the library
 *
 * @param    $post_id  The post identifier
 * @param    $entry     The entry
 * @param    $form      The form
 */
function gf_add_to_media_library ( $post_id, $feed, $entry, $form ) {
	
  foreach($form['fields'] as $field){

  //get media upload dir
    $uploads = wp_upload_dir();     
    $uploads_dir = $uploads['path'];      
    $uploads_url = $uploads['url'];
    
  //if its a custom field with input type file upload. 
  if( $field['type'] == 'post_custom_field' && $field['inputType'] == 'fileupload'){
    $entry_id = $field['id'];
    $files = rgar ( $entry, $entry_id );
    $custom_field = $field['postCustomFieldName']; //custom field key
    
  //if file field is not empty or not []
    if ( $files !== '' && $files !== "[]"){
	    

      $patterns = ['[', ']', '"']; //get rid of crap
      $file_entry = str_replace($patterns, '', $files);
      $files = explode ( ',', $file_entry  );

        foreach ($files as $file) {
          //each file is a url
          //get the filename from end of url in match[1]
            $filename = pathinfo($file, PATHINFO_FILENAME);
            //add to media library
            //WordPress API for image uploads.
            include_once( ABSPATH . 'wp-admin/includes/image.php' );
            include_once( ABSPATH . 'wp-admin/includes/file.php' );
            include_once( ABSPATH . 'wp-admin/includes/media.php' );
           
			$url = $file;
			$tmp = download_url( $url );
 
			$file_array = array(
			    'name' => basename( $url ),
			    'tmp_name' => $tmp
			);
 
           
            $new_url = stripslashes($file);
	        $result = media_handle_sideload( $file_array, $post_id );
            //saving the image to field or thumbnail
           
            
/*
            if( strpos($field['cssClass'], 'thumb') === false  ){
              $attachment_ids = (int)  get_attachment_id_from_src($result);
            }
            else{
              set_post_thumbnail($post_id, (int)  get_attachment_id_from_src($result) );
            }
*/
             
        } //end foreach file
        if ( isset( $result	 ) ){
          update_post_meta ($post_id, $custom_field, $result);
        }
      } //end if files not empty
    } //end if custom field of uploadfile
  } 
} //end for each form field


function get_attachment_id_from_src($image_src) {
  global $wpdb;
  $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
  $id = $wpdb->get_var($query);  
  return $id;
}

add_action( 'gform_advancedpostcreation_post_after_creation', 'gf_add_to_media_library', 10, 4 );


// Topic Subscription emails	

function resource_subscriptions() {

	if(date('t') == date('d')) {
		
		$args = array(
			'meta_key' => 'otf_subscription',
			'meta_compare' => 'EXISTS',
		);
	
		$users = get_users($args);
	
		foreach($users as $u) {
			$user_digest = get_user_meta($u->ID,'otf_subscription',true);						
			generate_digest($user_digest, $u);		
		}
	
	}
	
	return;
}

function generate_digest($topics, $user) {
	
	$today = getdate();
	
	$args = array(
		'post_type' => 'resource',
		'post_status' => 'published',
		'posts_per_page' => -1,
		'tax_query' => array(
			'taxonomy' => 'topic',
			'field' => 'term_id',
			'terms' => $topics,
			'operator' => 'IN',				
		),
		'orderby' => 'ASC',
		'date_query' => array(
			'month' => $today['mon'],
		),
	);

	$posts = get_posts($args);
		
	$output = '<h1 style="text-align: center;">Opioid Action Center New Resources for '.date("F", strtotime("today")).'</h1><hr>';
	
	foreach($posts as $post) {
		
		$output .= '<div style="padding-bottom: 1em; margin-bottom: 1em; border-bottom: 1px solid #eee;">';
			$output .= '<h4><a href="'.get_post_permalink($post->ID).'" title="'.$post->post_title.'">'.$post->post_title.'</a></h4>';
			$output .= '<p>'.get_the_content($post->ID).'</p>';
		$output .= '</div>';

	}
	
	$output .= '<p style="text-align: center">Find more at the <a href="https://opioidactioncenter.com" title="Opioid Action Center">Opioid Action Center</a></p>';
	
	$headers = 'From: Opioid Action Center <noreply@opioidactioncenter.com' . "\r\n";
	
	$mail = wp_mail($user->user_email, 'Opioid Action Center updates for '.date("F", strtotime("today")), $output, $headers);
	
}
	
add_action('resource_subscriptions_hook', 'resource_subscriptions');