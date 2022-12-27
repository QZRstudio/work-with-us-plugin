<?php

/* 
	This is the main filter that adds the CTA to the content
	only if the post has the tag that is associated to the CTA
	and if the CTA is active
	or if the admin is previewing the CTA
*/
function qzr_add_cta_if_needed($content) {

	if ( is_admin() ) {
		return $content;
	}

	global $post;
	$ctas = qzr_get_cta_data();
	$tags = wp_get_post_tags($post->ID);
	
	// Check if the post has tags
	if ( !empty($tags) ) {
		// Loop through the tags
		foreach ($tags as $tag) {
			if ( 
				isset($ctas[$tag->term_id]) && $ctas[$tag->term_id]['status'] || // Check if the CTA is active
				isset($ctas[$tag->term_id]) && isset($_GET['qzr-cta-preview']) && $_GET['qzr-cta-preview'] == 'true' && user_can( get_current_user_id(), 'manage_options' ) // Check if is a preview
				) {
				// Add the CTA to the content
				$content = qzr_inject_cta_to_the_content($content, $ctas[$tag->term_id]);
			}
		}
	}
	return $content;
}
add_filter('the_content', 'qzr_add_cta_if_needed', 10, 1);