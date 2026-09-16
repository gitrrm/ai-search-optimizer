<?php

namespace ASO\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RecommendationService {

	public function generate( $results ) {
		$recommendations = array();

		if ( empty( $results['schema']['status'] ) ) {
			$recommendations[] = array(
				'key'      => 'schema',
				'priority' => 'high',
				'title'    => 'Add structured data',
				'message'  => 'Add valid Schema.org JSON-LD to help machines understand your website content and entities.',
			);
		}

		if ( empty( $results['opengraph']['status'] ) ) {
			$missing = ! empty( $results['opengraph']['missing'] ) ? $results['opengraph']['missing'] : array();
			$message = 'Add Open Graph metadata to the homepage.';
			if ( ! empty( $missing ) ) {
				$message .= ' Missing: ' . implode( ', ', $missing ) . '.';
			}
			$recommendations[] = array(
				'key'      => 'opengraph',
				'priority' => 'medium',
				'title'    => 'Add Open Graph metadata',
				'message'  => $message,
			);
		}

		if ( empty( $results['robots']['available'] ) ) {
			$recommendations[] = array(
				'key'      => 'robots',
				'priority' => 'high',
				'title'    => 'Make robots.txt available',
				'message'  => 'Ensure your website exposes a valid robots.txt file and review crawler access rules.',
			);
		} elseif ( ! empty( $results['robots']['ai_crawlers'] ) ) {
			foreach ( $results['robots']['ai_crawlers'] as $crawler => $crawler_data ) {
				if ( ! empty( $crawler_data['disallow_all'] ) ) {
					$recommendations[] = array(
						'key'      => 'robots-' . sanitize_title( $crawler ),
						'priority' => 'high',
						'title'    => 'Review AI crawler access',
						'message'  => $crawler . ' is completely disallowed by robots.txt. Review this rule if you want this crawler to access your content.',
					);
				}
			}
		}

		if ( empty( $results['llms']['status'] ) ) {
			$recommendations[] = array(
				'key'      => 'llms',
				'priority' => 'medium',
				'title'    => 'Consider adding llms.txt',
				'message'  => 'Consider publishing an llms.txt file with useful information about your site and important content.',
			);
		}

		if ( empty( $results['faq']['status'] ) ) {
			$recommendations[] = array(
				'key'      => 'faq',
				'priority' => 'low',
				'title'    => 'Consider adding FAQ content',
				'message'  => 'Consider adding useful FAQ content where appropriate. FAQ structured data should represent real, visible FAQ content.',
			);
		}

		return $recommendations;
	}
}
