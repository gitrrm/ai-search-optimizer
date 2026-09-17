<?php

namespace ASO\Admin;

use ASO\Admin\Controllers\DashboardController;
use ASO\Admin\Pages\SettingsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {

		add_action(
			'admin_menu',
			array(
				$this,
				'register_menu',
			)
		);

		add_action(
			'admin_post_aso_run_analysis',
			array(
				$this,
				'handle_run_analysis',
			)
		);
	}

	public function register_menu() {

		$page = new SettingsPage();

		add_menu_page(
			__( 'AI Search Optimizer', 'ai-search-optimizer' ),
			__( 'AI Optimizer', 'ai-search-optimizer' ),
			'manage_options',
			'ai-search-optimizer',
			array(
				$page,
				'render',
			),
			'dashicons-chart-line',
			80
		);
	}

	/**
	 * Handle manual analysis request.
	 *
	 * @return void
	 */
	public function handle_run_analysis() {

		$controller = new DashboardController();

		$controller->handle_run_analysis();
	}
}