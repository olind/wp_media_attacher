<?php

$unattached = get_all_unattached_media();
_p('Unattached media count: ' . count($unattached));

foreach($unattached as $ua){

	$filename = get_filename_from_attachment($ua);
	_p("Searching matching posts for attachment: '" . $filename ."' (ID: " . $ua->ID . ")");

	$matching_post = search_post_content($filename);

	if($matching_post){
		_p("   Matching post: '" . $matching_post[0]->post_title . "' (ID: " . $matching_post[0]->ID . ")");
	} else {
		
	}
	
}

wp_reset_postdata();

/* - - - - - */

/**
 * Attaches the given media attachment to the given post
 */
function attach_media_to_post($media, $post){
	$args = array(
		'ID' => $media->ID,);

	_p(wp_insert_attachment($args, false, $post->ID));
}

/**
 * Prints a message when the script is run with --debug. 
 * Accepts both strings and arrays.
 */
function _p($array){
	WP_CLI::debug(print_r($array, true),  $group = false);
}

/**
 * Gets filename without extension from full filepath
 */
function get_filename_from_attachment($attachment){
	return pathinfo(get_attached_file($attachment->ID))['filename'];
}

/**
 * Get a list of all unatached media files in the library
 */
function get_all_unattached_media(){
	$the_query = array(
		'post_type' => 'attachment',
		'numberposts' => /*-1*/15,
		'post_status' => null,
		'post_parent' => 0
	); 
	$attachments = get_posts($the_query);
	return $attachments;
}

/** 
 * Searches all posts for given string. 
 * Returns array of matching posts or null if no posts are found.
 */
function search_post_content($search_string){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->posts} WHERE post_content LIKE '%" . $search_string . "%' AND post_type = 'post';";
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

function search_post_title ($search_string){
	WP_CLI::log("Finding posts");
		
	$the_query = new WP_Query(array( 
		's'		=>	$search_string,
		'posts_per_page' => '-1'
	));
	
	if ( $the_query->have_posts() ) {
	    while ( $the_query->have_posts() ) {
	        $the_query->the_post();
	        _p(" P: " . get_the_post());
	    }
	} else {
		_p("No posts found");
	}
	_p("Total posts found: " . count( $args->posts ));
}


function get_attachments(){
$media = get_attached_media( 'image', 19147 /*get_the_post()*/ );
_p($media);
foreach ($media as $image) {  
	$ximage =  wp_get_attachment_image_src($image->ID,'medium');
	_p($ximage[0]);
}
}


?>