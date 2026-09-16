<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OpenGraphDetector {

	/**
	 * Analyze Open Graph metadata on the homepage.
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
				'tags'    => array(),
				'missing' => $this->get_required_tags(),
				'error'   => $response->get_error_message(),
			);
		}

		$html = wp_remote_retrieve_body( $response );

		if ( empty( $html ) ) {
			return array(
				'status'  => false,
				'tags'    => array(),
				'missing' => $this->get_required_tags(),
				'error'   => 'Homepage returned an empty response.',
			);
		}

		$tags = $this->extract_open_graph_tags( $html );

		$required_tags = $this->get_required_tags();
		$missing       = array();

		foreach ( $required_tags as $tag ) {

			if ( empty( $tags[ $tag ] ) ) {
				$missing[] = $tag;
			}
		}

		return array(
			'status'  => empty( $missing ),
			'tags'    => $tags,
			'missing' => $missing,
			'error'   => '',
		);
	}

	/**
	 * Backward-compatible boolean check.
	 *
	 * @return bool
	 */
	public function has_opengraph() {

		$result = $this->analyze();

		return ! empty( $result['status'] );
	}

	/**
	 * Extract Open Graph meta tags.
	 *
	 * @param string $html Homepage HTML.
	 * @return array
	 */
	private function extract_open_graph_tags( $html ) {

		$tags = array();

		/*
		 * Extract individual meta elements first.
		 * This allows property/content attributes to appear
		 * in either order.
		 */
		$pattern = '/<meta\b[^>]*>/i';

		if ( ! preg_match_all( $pattern, $html, $matches ) ) {
			return $tags;
		}

		foreach ( $matches[0] as $meta_tag ) {

			$property = $this->get_attribute_value(
				$meta_tag,
				'property'
			);

			if ( empty( $property ) ) {
				continue;
			}

			$property = strtolower( trim( $property ) );

			if ( 0 !== strpos( $property, 'og:' ) ) {
				continue;
			}

			$content = $this->get_attribute_value(
				$meta_tag,
				'content'
			);

			if ( null === $content ) {
				continue;
			}

			$tags[ $property ] = trim(
				html_entity_decode(
					$content,
					ENT_QUOTES,
					'UTF-8'
				)
			);
		}

		return $tags;
	}

	/**
	 * Get an HTML attribute value.
	 *
	 * @param string $html_tag HTML tag.
	 * @param string $attribute Attribute name.
	 * @return string|null
	 */
	private function get_attribute_value( $html_tag, $attribute ) {

		$pattern = '/\b' . preg_quote( $attribute, '/' ) . '\s*=\s*([\'"])(.*?)\1/is';

		if ( preg_match( $pattern, $html_tag, $matches ) ) {
			return $matches[2];
		}

		return null;
	}

	/**
	 * Required Open Graph properties.
	 *
	 * @return array
	 */
	private function get_required_tags() {

		return array(
			'og:title',
			'og:description',
			'og:image',
			'og:url',
		);
	}
}