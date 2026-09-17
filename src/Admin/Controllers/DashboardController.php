<?php

namespace ASO\Admin\Controllers;

use ASO\Services\AnalyzerService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardController {

	/**
	 * Analysis cache key.
	 *
	 * @var string
	 */
	private const CACHE_KEY = 'aso_analysis_result_v1';

	/**
	 * Analysis cache duration.
	 *
	 * @var int
	 */
	private const CACHE_TTL = HOUR_IN_SECONDS;

	/**
	 * Get dashboard data.
	 *
	 * Uses a cached analysis result when available.
	 *
	 * @return array
	 */
	public function get_dashboard_data() {

		$cached_result = get_transient( self::CACHE_KEY );

		if ( false !== $cached_result && is_array( $cached_result ) ) {
			$result = $cached_result;
		} else {
			$analyzer = new AnalyzerService();
			$result   = $analyzer->analyze();

			set_transient(
				self::CACHE_KEY,
				$result,
				self::CACHE_TTL
			);
		}

		return array(
			'ai_score'        => $result['score'],
			'max_score'       => $result['max'],
			'checks'          => $result['checks'],
			'results'         => $result['results'],
			'recommendations' => $result['recommendations'],
		);
	}

	/**
	 * Handle a manual analysis request.
	 *
	 * @return void
	 */
	public function handle_run_analysis() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to run an analysis.', 'ai-search-optimizer' ),
				esc_html__( 'Permission denied', 'ai-search-optimizer' ),
				array( 'response' => 403 )
			);
		}

		check_admin_referer( 'aso_run_analysis' );

		delete_transient( self::CACHE_KEY );

		$redirect_url = add_query_arg(
			array(
				'page'          => 'ai-search-optimizer',
				'aso_analysis' => 'success',
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}