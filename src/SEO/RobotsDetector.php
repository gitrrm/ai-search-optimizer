<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RobotsDetector {

	public function has_robots() {

		if ( $this->check_wp_robots() ) {
			return true;
		}

		if ( $this->check_virtual_robots() ) {
			return true;
		}

		if ( $this->check_file_robots() ) {
			return true;
		}

		if ( $this->check_blog_public() ) {
			return true;
		}

		return false;
	}

	private function check_wp_robots() {

		if ( ! function_exists( 'wp_robots' ) ) {
			return false;
		}

		$robots = apply_filters(
			'wp_robots',
			array()
		);

		return ! empty( $robots );
	}

	private function check_virtual_robots() {

		$response = wp_remote_get(
			home_url( '/?robots=1' )
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body(
			$response
		);

		return false !== stripos(
			$body,
			'user-agent'
		);
	}

	private function check_file_robots() {

		$file = ABSPATH . 'robots.txt';

		return file_exists( $file );
	}

	private function check_blog_public() {

		return (
			'1' === get_option(
				'blog_public'
			)
		);
	}
}