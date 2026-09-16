<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SchemaDetector {

	/**
	 * Analyze JSON-LD schema on the homepage.
	 *
	 * @return array
	 */
	public function analyze() {

		$response = wp_safe_remote_get(
			home_url( '/' ),
			array(
				'timeout' => 8,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'status'  => false,
				'types'   => array(),
				'count'   => 0,
				'error'   => $response->get_error_message(),
			);
		}

		$html = wp_remote_retrieve_body( $response );

		if ( empty( $html ) ) {
			return array(
				'status'  => false,
				'types'   => array(),
				'count'   => 0,
				'error'   => 'Homepage returned an empty response.',
			);
		}

		$blocks = $this->extract_json_ld( $html );
		$types  = array();

		foreach ( $blocks as $block ) {
			$this->collect_types( $block, $types );
		}

		$types = array_values( array_unique( $types ) );

		return array(
			'status' => ! empty( $types ),
			'types'  => $types,
			'count'  => count( $types ),
			'error'  => '',
		);
	}

	/**
	 * Backward-compatible boolean check.
	 *
	 * @return bool
	 */
	public function has_schema() {

		$result = $this->analyze();

		return ! empty( $result['status'] );
	}

	/**
	 * Extract JSON-LD blocks from HTML.
	 *
	 * @param string $html Homepage HTML.
	 * @return array
	 */
	private function extract_json_ld( $html ) {

		$blocks = array();

		$pattern = '/<script[^>]+type=[\'"]application\/ld\+json[\'"][^>]*>(.*?)<\/script>/is';

		if ( ! preg_match_all( $pattern, $html, $matches ) ) {
			return $blocks;
		}

		foreach ( $matches[1] as $json ) {

			$data = json_decode(
				html_entity_decode( trim( $json ) ),
				true
			);

			if ( JSON_ERROR_NONE === json_last_error() && is_array( $data ) ) {
				$blocks[] = $data;
			}
		}

		return $blocks;
	}

	/**
	 * Recursively collect Schema.org @type values.
	 *
	 * @param mixed $data   Decoded JSON-LD data.
	 * @param array $types  Collected schema types.
	 * @return void
	 */
	private function collect_types( $data, &$types ) {

		if ( ! is_array( $data ) ) {
			return;
		}

		if ( isset( $data['@type'] ) ) {

			$type_values = (array) $data['@type'];

			foreach ( $type_values as $type ) {

				if ( is_string( $type ) && '' !== trim( $type ) ) {
					$types[] = trim( $type );
				}
			}
		}

		foreach ( $data as $value ) {

			if ( is_array( $value ) ) {
				$this->collect_types( $value, $types );
			}
		}
	}
}