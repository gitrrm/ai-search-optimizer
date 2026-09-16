<?php

namespace ASO\Services;

use ASO\SEO\FaqDetector;
use ASO\SEO\LlmsDetector;
use ASO\SEO\OpenGraphDetector;
use ASO\SEO\RobotsDetector;
use ASO\SEO\SchemaDetector;

if (! defined('ABSPATH')) {
	exit;
}

class AnalyzerService
{

	/**
	 * Run the complete site analysis.
	 *
	 * @return array
	 */
	public function analyze()
	{

		$results = array(
			'schema'    => $this->analyze_schema(),
			'opengraph' => $this->analyze_opengraph(),
			'robots'    => $this->analyze_robots(),
			'llms'      => $this->analyze_llms(),
			'faq'       => $this->analyze_faq(),
		);

		$scoring_service = new ScoringService();

		$score = $scoring_service->calculate($results);

		$recommendation_service = new RecommendationService();

		$recommendations = $recommendation_service->generate($results);

		return array(
			'score'           => $score['score'],
			'max'             => $score['max_score'],
			'checks'          => $score['checks'],
			'results'         => $results,
			'recommendations' => $recommendations,
		);
	}

	/**
	 * Analyze schema.
	 *
	 * @return array
	 */
	private function analyze_schema()
	{

		$detector = new SchemaDetector();

		return $detector->analyze();
	}

	/**
	 * Analyze Open Graph metadata.
	 *
	 * @return array
	 */
	private function analyze_opengraph()
	{

		$detector = new OpenGraphDetector();

		return $detector->analyze();
	}

	/**
	 * Analyze robots.txt.
	 *
	 * @return array
	 */
	private function analyze_robots()
	{

		$detector = new RobotsDetector();

		return $detector->analyze();
	}

	/**
	 * Analyze llms.txt.
	 *
	 * @return array
	 */
	private function analyze_llms()
	{

		$detector = new LlmsDetector();

		return $detector->analyze();
	}

	/**
	 * Analyze FAQ content.
	 *
	 * @return array
	 */
	private function analyze_faq()
	{

		$detector = new FaqDetector();

		return $detector->analyze();
	}
}
