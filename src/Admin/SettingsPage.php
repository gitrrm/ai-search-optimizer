<?php

namespace ASO\Admin\Pages;

use ASO\Admin\Controllers\DashboardController;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsPage {

	public function render() {

		$controller = new DashboardController();

		$data = $controller->get_dashboard_data();

		include ASO_PLUGIN_PATH . 'src/Admin/Views/dashboard.php';
	}
}