<?php

namespace ASO\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ScoringService {

	private $max_score = 100;

	public function calculate( $results ) {
		$checks = array();

		$checks['schema'] = $this->score_check( 'Schema', 20, ! empty( $results['schema']['status'] ), $results['schema'] );
		$checks['opengraph'] = $this->score_check( 'Open Graph', 20, ! empty( $results['opengraph']['status'] ), $results['opengraph'] );
		$checks['robots'] = $this->score_check( 'AI Crawler Access', 20, $this->robots_passed( $results['robots'] ), $results['robots'] );
		$checks['llms'] = $this->score_check( 'llms.txt', 20, ! empty( $results['llms']['status'] ), $results['llms'] );
		$checks['faq'] = $this->score_check( 'FAQ Content', 20, ! empty( $results['faq']['status'] ), $results['faq'] );

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

	private function robots_passed( $robots ) {
		if ( empty( $robots['available'] ) ) {
			return false;
		}

		if ( ! empty( $robots['ai_crawlers'] ) ) {
			foreach ( $robots['ai_crawlers'] as $crawler ) {
				if ( ! empty( $crawler['disallow_all'] ) ) {
					return false;
				}
			}
		}

		return true;
	}

	private function score_check( $label, $weight, $passed, $details ) {
		return array(
			'label'   => $label,
			'passed'  => $passed,
			'weight'  => $weight,
			'earned'  => $passed ? $weight : 0,
			'details' => $details,
		);
	}
}
