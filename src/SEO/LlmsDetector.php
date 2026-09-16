<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LlmsDetector {

	/**
	 * Analyze llms.txt and AI crawler information.
	 *
	 * @return array
	 */
	public function analyze() {

		$llms_result = $this->check_llms_file();

		return array(
			'llms_file' => $llms_result,
			'status'    => ! empty( $llms_result['available'] ),
		);
	}

	/**
	 * Check whether llms.txt is available.
	 *
	 * @return array
	 */
	private function check_llms_file() {

		$response = wp_safe_remote_get(
			home_url( '/llms.txt' ),
			array(
				'timeout' => 8,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'available' => false,
				'status'    => 0,
				'error'     => $response->get_error_message(),
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );

		if ( 200 !== $status_code ) {
			return array(
				'available' => false,
				'status'    => $status_code,
				'error'     => 'llms.txt was not found.',
			);
		}

		if ( empty( trim( $body ) ) ) {
			return array(
				'available' => false,
				'status'    => $status_code,
				'error'     => 'llms.txt exists but is empty.',
			);
		}

		return array(
			'available' => true,
			'status'    => $status_code,
			'error'     => '',
		);
	}
}