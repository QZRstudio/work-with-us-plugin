<?php

	/* 
		These are the main functions that get the CTA data
		and save the CTA data.
		Everything is saved in the wp_options table just using
		the regular WordPress functions (get_option, update_option).

		The value of the option is an array of arrays, structured like this:
		$ctas = array(
			$tag_ID => array(
				'status' => true, // true or false
				'html' => '<div>HTML</div>', // HTML code
				'tag_ID' => $tag_ID, // the tag ID
				'rule' => array(
					'callback' => 'qzr_rule_after_nth_element', // Which function to use to check if the CTA should be shown
					'options' => array(
						'element' => 'p', // The element to find
						'nth' => 2 // The nth element to check
					)
				)
			)
		);
	
	*/

	// Get all the CTAS saved in the options table
	function qzr_get_cta_data() {
		$cta_data = get_option( 'ilpost_plugin_settings' );
		
		if ( empty($cta_data) ) {
			update_option( 'ilpost_plugin_settings', array() );
		}

		return $cta_data;
	}

	// Get a single CTA by tag ID
	function qzr_get_cta($tag_ID = false) {

		if ( !$tag_ID ) {
			return false;
		}

		$cta_data = qzr_get_cta_data();

		// Default CTA data
		$result = array(
			'status' => false,
			'html' => '',
			'tag_ID' => $tag_ID,
			'rule' => array(
				'callback' => '',
				'options' => array()
			)
		);

		$result = isset($cta_data[$tag_ID])? $cta_data[$tag_ID] : $result;

		return $result;
	}

	// Save a single CTA by tag ID
	function qzr_update_cta($tag_ID, $value) {

		// if $input is not a valid tag ID or tag slug, return false
		if ( !$tag_ID ) {
			return false;
		}

		$cta_data = qzr_get_cta_data();
		$cta_data[$tag_ID] = $value;
		update_option( 'ilpost_plugin_settings', $cta_data );
	}

	// Return the content with the CTA injected
	function qzr_inject_cta_to_the_content($content, $cta) {
		return $cta['rule']['callback']( $content, $cta );
	}