<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LlmsDetector {

	public function analyze() {

		$result = array(
			'llms_file'      => false,
			'ai_robots'      => false,
			'schema_support' => false,
			'score'          => 0,
		);

		// Check llms.txt
		$response = wp_remote_get(
			home_url( '/llms.txt' )
		);

		if (
			! is_wp_error( $response )
			&&
			200 === wp_remote_retrieve_response_code(
				$response
			)
		) {

			$result['llms_file'] = true;

			$result['score'] += 10;
		}

		// Check robots AI directives
		$robots = wp_remote_get(
			home_url( '/robots.txt' )
		);

		if ( ! is_wp_error( $robots ) ) {

			$body = wp_remote_retrieve_body(
				$robots
			);

			if (
				false !== stripos(
					$body,
					'GPTBot'
				)
				||
				false !== stripos(
					$body,
					'Google-Extended'
				)
				||
				false !== stripos(
					$body,
					'CCBot'
				)
			) {

				$result['ai_robots'] = true;

				$result['score'] += 5;
			}
		}

		// Schema fallback
		$schema = new SchemaDetector();

		if ( $schema->has_schema() ) {

			$result['schema_support'] = true;

			$result['score'] += 5;
		}

		return $result;
	}
}