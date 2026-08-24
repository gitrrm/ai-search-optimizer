<?php

namespace ASO\Services;

use ASO\SEO\SchemaDetector;
use ASO\SEO\OpenGraphDetector;
use ASO\SEO\RobotsDetector;
use ASO\SEO\LlmsDetector;
use ASO\SEO\FaqDetector;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AnalyzerService {

	public function analyze() {

		$schema = new SchemaDetector();

		$opengraph = new OpenGraphDetector();

		$robots = new RobotsDetector();

		$llms = new LlmsDetector();

		$faq = new FaqDetector();

		$llms_result = $llms->analyze();

		$faq_result = $faq->analyze();

		$checks = array(

			'schema' => array(
				'status' => $schema->has_schema(),
				'weight' => 20,
			),

			'opengraph' => array(
				'status' => $opengraph->has_opengraph(),
				'weight' => 20,
			),

			'robots' => array(
				'status' => $robots->has_robots(),
				'weight' => 20,
			),

			'llms' => array(
				'status' => (
					$llms_result['score'] > 0
				),

				'weight' => $llms_result['score'],
			),

			'faq' => array(
				'status' => (
					$faq_result['score'] > 0
				),

				'weight' => $faq_result['score'],
			),
		);

		$score = 0;

		foreach ( $checks as $check ) {

			if ( $check['status'] ) {
				$score += $check['weight'];
			}
		}

		return array(
			'score'  => $score,
			'checks' => $checks,
		);
	}
}