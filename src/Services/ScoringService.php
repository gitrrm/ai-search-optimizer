<?php

namespace ASO\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ScoringService {

	/**
	 * Maximum score.
	 *
	 * @var int
	 */
	private $max_score = 100;

	/**
	 * Calculate the AI visibility score.
	 *
	 * @param array $results Detector results.
	 * @return array
	 */
	public function calculate( $results ) {

		$checks = array();

		$checks['schema'] = $this->score_schema(
			$results['schema']
		);

		$checks['opengraph'] = $this->score_opengraph(
			$results['opengraph']
		);

		$checks['robots'] = $this->score_robots(
			$results['robots']
		);

		$checks['llms'] = $this->score_llms(
			$results['llms']
		);

		$checks['faq'] = $this->score_faq(
			$results['faq']
		);

		$score = 0;

		foreach ( $checks as $check ) {
			$score += $check['earned'];
		}

		return array(
			'score'     => min( $score, $this->max_score ),
			'max_score' => $this->max_score,
			'checks'    => $checks,
		);
	}

	/**
	 * Score Schema.
	 *
	 * @param array $schema Schema detector result.
	 * @return array
	 */
	private function score_schema( $schema ) {

		$earned = 0;
		$details = array();

		if ( ! empty( $schema['status'] ) ) {

			$earned += 10;

			$details[] = 'JSON-LD detected.';
		}

		if ( ! empty( $schema['types'] ) ) {

			$earned += 5;

			$details[] = count( $schema['types'] ) . ' Schema.org type(s) detected.';
		}

		$useful_types = array(
			'Organization',
			'Person',
			'WebSite',
			'WebPage',
			'Article',
			'Product',
			'LocalBusiness',
		);

		if ( ! empty( $schema['types'] ) ) {

			foreach ( $schema['types'] as $type ) {

				if ( in_array( $type, $useful_types, true ) ) {

					$earned += 5;

					$details[] = 'Relevant Schema.org entity/content type detected.';
					break;
				}
			}
		}

		return array(
			'label'   => 'Schema',
			'passed'  => $earned >= 15,
			'weight'  => 20,
			'earned'  => min( $earned, 20 ),
			'details' => $details,
		);
	}

	/**
	 * Score Open Graph metadata.
	 *
	 * @param array $opengraph Open Graph detector result.
	 * @return array
	 */
	private function score_opengraph( $opengraph ) {

		$required_tags = array(
			'og:title',
			'og:description',
			'og:image',
			'og:url',
		);

		$earned = 0;
		$details = array();

		foreach ( $required_tags as $tag ) {

			if (
				! empty( $opengraph['tags'][ $tag ] )
			) {
				$earned += 5;
				$details[] = $tag . ' detected.';
			} else {
				$details[] = $tag . ' is missing.';
			}
		}

		return array(
			'label'   => 'Open Graph',
			'passed'  => 20 === $earned,
			'weight'  => 20,
			'earned'  => $earned,
			'details' => $details,
		);
	}

	/**
	 * Score robots configuration.
	 *
	 * @param array $robots Robots detector result.
	 * @return array
	 */
	private function score_robots( $robots ) {

		$earned = 0;
		$details = array();

		if ( ! empty( $robots['available'] ) ) {

			$earned += 10;

			if ( 'wordpress' === $robots['source'] ) {
				$details[] = 'WordPress-generated robots.txt detected.';
			} else {
				$details[] = 'robots.txt detected.';
			}
		} else {

			$details[] = 'robots.txt is not available.';
		}

		$ai_access_ok = true;

		if ( ! empty( $robots['ai_crawlers'] ) ) {

			foreach ( $robots['ai_crawlers'] as $crawler => $crawler_data ) {

				if ( ! empty( $crawler_data['disallow_all'] ) ) {

					$ai_access_ok = false;

					$details[] = $crawler . ' is completely blocked.';
				}
			}
		}

		if ( $ai_access_ok && ! empty( $robots['available'] ) ) {

			$earned += 10;

			$details[] = 'No complete block detected for the checked AI crawlers.';
		}

		return array(
			'label'   => 'AI Crawler Access',
			'passed'  => 20 === $earned,
			'weight'  => 20,
			'earned'  => $earned,
			'details' => $details,
		);
	}

	/**
	 * Score llms.txt.
	 *
	 * @param array $llms llms.txt detector result.
	 * @return array
	 */
	private function score_llms( $llms ) {

		$earned  = 0;
		$details = array();

		if ( ! empty( $llms['status'] ) ) {

			$earned = 20;

			$details[] = 'llms.txt is available and non-empty.';
		} else {

			$details[] = 'llms.txt is not available.';
		}

		return array(
			'label'   => 'llms.txt',
			'passed'  => 20 === $earned,
			'weight'  => 20,
			'earned'  => $earned,
			'details' => $details,
		);
	}

	/**
	 * Score FAQ content.
	 *
	 * @param array $faq FAQ detector result.
	 * @return array
	 */
	private function score_faq( $faq ) {

		$earned  = 0;
		$details = array();

		if ( ! empty( $faq['faq_blocks'] ) ) {

			$earned += 10;

			$details[] = 'FAQ content detected.';
		}

		if ( ! empty( $faq['faq_schema'] ) ) {

			$earned += 10;

			$details[] = 'FAQPage schema detected.';
		}

		if ( 0 === $earned ) {

			$details[] = 'No FAQ content or FAQPage schema detected.';
		}

		return array(
			'label'   => 'FAQ Content',
			'passed'  => $earned >= 10,
			'weight'  => 20,
			'earned'  => $earned,
			'details' => $details,
		);
	}
}