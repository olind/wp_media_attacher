<?php
/*
Place this file in wp-content/flickr-attacher.php
To run it you WP-CLI: wp eval-file flickr-attacher.php
*/

//find_flickr_post();
//get_attachments();
get_all_unattached_media();

function d_array($array){
	WP_CLI::debug(print_r($array, true),  $group = false);
}
function d_string($string){
	WP_CLI::debug(print_r($string, true),  $group = false);
}

function get_attachments(){
	$media = get_attached_media( 'image', 19147 /*get_the_post()*/ );
	d_array($media);

	foreach ($media as $image) {  
		$ximage =  wp_get_attachment_image_src($image->ID,'medium');
		WP_CLI::debug($ximage[0]);
	}
	
}

/**
 * Get a list of all unatached media files in the library
 */
function get_all_unattached_media(){
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => null,
		'post_parent' => 0
	); 
	$attachments = get_posts($args);

	d_string('Unattached files count: ' . count($attachments));
	 
	 if ($attachments) {
		foreach ($attachments as $post) {
			setup_postdata($post);
			//d_array($post);
			//d_string(wp_get_attachment_link($post->ID));
		}
	 }
}

function find_flickr_post (){
	WP_CLI::log("Finding posts");
		
	$the_query = new WP_Query(array( 
		's'		=>	'/flickr/',
		'cat'	=>	1050,
		'posts_per_page' => '-1'
	));
	
	if ( $the_query->have_posts() ) {
	    while ( $the_query->have_posts() ) {
	        $the_query->the_post();
			

	        //WP_CLI::line(" P: " . get_the_title());

	    }
	} else {
		WP_CLI::warning("No posts found");
	}
	
	WP_CLI::line("Total posts found: " . count( $the_query->posts ));
	wp_reset_postdata(); //Restore original Post Data
}

?>