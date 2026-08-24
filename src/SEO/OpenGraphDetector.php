<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OpenGraphDetector {

	public function has_opengraph() {

		$response = wp_remote_get(
			home_url('/')
		);

		if ( is_wp_error( $response ) ) {
			return $response->get_error_message();
		}

		$html = wp_remote_retrieve_body(
			$response
		);

		// TEMP DEBUG
		update_option(
			'aso_og_debug',
			substr(
				$html,
				0,
				5000
			)
		);

		return (
			false !== stripos(
				$html,
				'og:'
			)
		);
	}
}