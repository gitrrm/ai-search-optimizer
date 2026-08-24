<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SchemaDetector {

	// public function has_schema() {

	// 	global $wp_query;

	// 	ob_start();

	// 	do_action( 'wp_head' );

	// 	$content = ob_get_clean();

	// 	if (
	// 		false !== strpos(
	// 			$content,
	// 			'application/ld+json'
	// 		)
	// 	) {
	// 		return true;
	// 	}

	// 	return false;
	// }

	public function has_schema() {

	$response = wp_remote_get(
		home_url('/')
	);

	if ( is_wp_error( $response ) ) {

		return $response->get_error_message();

	}

	return false;
}
}