<?php

namespace ASO\Admin;

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
}