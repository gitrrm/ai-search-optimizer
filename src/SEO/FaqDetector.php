<?php

namespace ASO\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FaqDetector {

	public function analyze() {
		$post_id = $this->get_front_page_id();

		if ( empty( $post_id ) ) {
			return array(
				'status'     => false,
				'faq_schema' => false,
				'faq_blocks' => false,
				'faq_count'  => 0,
				'error'      => 'A static front page could not be identified.',
			);
		}

		$content = get_post_field( 'post_content', $post_id );

		if ( empty( $content ) ) {
			return array(
				'status'     => false,
				'faq_schema' => false,
				'faq_blocks' => false,
				'faq_count'  => 0,
				'error'      => 'The front page does not contain any content.',
			);
		}

		$faq_items  = $this->extract_faq_blocks( $content );
		$faq_schema = $this->has_faq_schema( $content );

		return array(
			'status'     => $faq_schema || ! empty( $faq_items ),
			'faq_schema' => $faq_schema,
			'faq_blocks' => ! empty( $faq_items ),
			'faq_count'  => count( $faq_items ),
			'error'      => '',
		);
	}

	public function has_faq() {
		$result = $this->analyze();
		return ! empty( $result['status'] );
	}

	private function get_front_page_id() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			return (int) get_option( 'page_on_front' );
		}
		return (int) get_option( 'page_for_posts' );
	}

	private function has_faq_schema( $content ) {
		if ( false !== stripos( $content, '"@type":"FAQPage"' ) ) {
			return true;
		}
		return false !== stripos( $content, '"@type": "FAQPage"' );
	}

	private function extract_faq_blocks( $content ) {
		$blocks = parse_blocks( $content );
		$faq_items = array();
		$this->walk_blocks( $blocks, $faq_items );
		return $faq_items;
	}

	private function walk_blocks( $blocks, &$faq_items ) {
		foreach ( $blocks as $block ) {
			$block_name = isset( $block['blockName'] ) ? strtolower( (string) $block['blockName'] ) : '';

			if ( '' !== $block_name && false !== strpos( $block_name, 'faq' ) ) {
				$question = '';
				if ( isset( $block['attrs']['question'] ) ) {
					$question = $block['attrs']['question'];
				}
				if ( empty( $question ) && ! empty( $block['innerHTML'] ) ) {
					$question = wp_strip_all_tags( $block['innerHTML'] );
				}
				$question = trim( wp_strip_all_tags( $question ) );

				if ( '' !== $question ) {
					$faq_items[] = array(
						'question' => $question,
						'block'    => $block_name,
					);
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->walk_blocks( $block['innerBlocks'], $faq_items );
			}
		}
	}
}
