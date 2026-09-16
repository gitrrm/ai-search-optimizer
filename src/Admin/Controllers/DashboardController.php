<?php

namespace ASO\Admin\Controllers;

use ASO\Services\AnalyzerService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardController {

	public function get_dashboard_data() {
		$analyzer = new AnalyzerService();
		$result   = $analyzer->analyze();

		return array(
			'ai_score'        => $result['score'],
			'max_score'       => $result['max'],
			'checks'          => $result['checks'],
			'results'         => $result['results'],
			'recommendations' => $result['recommendations'],
		);
	}
}
