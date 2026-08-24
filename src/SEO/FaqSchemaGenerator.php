<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FaqSchemaGenerator {

	public function init() {

		add_action(
			'wp_head',
			array(
				$this,
				'output_schema',
			),
			999
		);
	}

	public function output_schema() {

		$post_id = get_queried_object_id();

		if ( empty( $post_id ) ) {
			return;
		}

		$content = get_post_field(
			'post_content',
			$post_id
		);

		if ( empty( $content ) ) {
			return;
		}

		$blocks = parse_blocks(
			$content
		);

		$faq_items = array();

		$this->extract_faq_blocks(
			$blocks,
			$faq_items
		);

		if ( empty( $faq_items ) ) {
			return;
		}

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $faq_items,
		);

		echo '<script type="application/ld+json">';

		echo wp_json_encode(
			$schema,
			JSON_UNESCAPED_SLASHES |
			JSON_UNESCAPED_UNICODE
		);

		echo '</script>';
	}

	private function extract_faq_blocks(
		$blocks,
		&$faq_items
	) {

		foreach ( $blocks as $block ) {

			if (
				! empty(
					$block['blockName']
				)
			) {

				$block_name =
					strtolower(
						$block['blockName']
					);

				if (
					false !== strpos(
						$block_name,
						'faq'
					)
				) {

					$question =
						$block['attrs']['question']
						?? '';

					$answer = '';

					if (
						! empty(
							$block['innerBlocks'][0]['innerHTML']
						)
					) {

						$answer =
							wp_strip_all_tags(
								$block['innerBlocks'][0]['innerHTML']
							);
					}

					if (
						! empty(
							$question
						)
					) {

						$faq_items[] = array(

							'@type' =>
								'Question',

							'name' =>
								wp_strip_all_tags(
									$question
								),

							'acceptedAnswer' =>
								array(

								'@type' =>
									'Answer',

								'text' =>
									$answer,
							),
						);
					}
				}
			}

			if (
				! empty(
					$block['innerBlocks']
				)
			) {

				$this->extract_faq_blocks(
					$block['innerBlocks'],
					$faq_items
				);
			}
		}
	}
}