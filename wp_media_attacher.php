<?php

$unattached = get_all_unattached_media();
_p('Unattached media count: ' . count($unattached));

foreach($unattached as $ua){

	$filename = pathinfo(get_attached_file($ua->ID))['filename'];

	_p("Searching matching posts for attachment: '" . $filename ."' (ID: " . $ua->ID . ")");
	$matching_post = search_post_content($filename, "post");
	if($matching_post){
		_p("   Matching post: '" . $matching_post[0]->post_title . "' (ID: " . $matching_post[0]->ID . ")");
		attach_media_to_post($ua, $matching_post[0]);
	}

	_p("Searching matching pages for attachment: '" . $filename ."' (ID: " . $ua->ID . ")");
	$matching_post = search_post_content($filename, "page");
	if($matching_post){
		_p("   Matching page: '" . $matching_post[0]->post_title . "' (ID: " . $matching_post[0]->ID . ")");
		attach_media_to_post($ua, $matching_post[0]);
	}
	
}

wp_reset_postdata();


/**
 * Attaches the given media attachment to the given post
 */
function attach_media_to_post($media, $post){

	_p("   Attaching " .$media->ID . " to post " . $post->ID);

	$fp = get_attached_file( $media->ID );
	$ft = wp_check_filetype( $fp );

	$media->post_mime_type = $ft['type'];
	$media->comment_status = closed;
	$media->post_title = $post->post_title . " (media)";

	$attach_id = wp_insert_attachment($media, $fp, $post->ID);	
	$attach_data = wp_generate_attachment_metadata( $attach_id, $fp );
	$update_status = wp_update_attachment_metadata( $attach_id, $attach_data );

	if(!empty($update_status)){
		_p(      "** FAILED");
	}
}

/**
 * Get a list of all unatached media files in the library
 */
function get_all_unattached_media(){
	$the_query = array(
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => null,
		'post_parent' => 0
	); 
	$attachments = get_posts($the_query);
	return $attachments;
}

/** 
 * Searches all posts for given string and page type (must be: post or page)
 * Returns array of matching posts or null if no posts are found.
 */
function search_post_content($search_string, $pagetype){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->posts} WHERE post_content LIKE '%" . $search_string . "%' AND post_type = '" . $pagetype . "';";
	$result = $wpdb->get_results($sql);

  	if ($result){
		_p("   Matching posts: '" . count($result) . "' - Searching for '" . $search_string . "'");
		$posts = array();
		foreach($result as $post){
			$posts[] = get_post($post->ID);
		}
		return $posts;
  	} else {
		_p("   No matched posts: " . $search_string);
		return null;
	}
}

/**
 * Prints a message when the script is run with --debug. 
 * Accepts both strings and arrays.
 */
function _p($array){
	WP_CLI::debug(print_r($array, true),  $group = false);
}