<?php
/**
 * Plugin Name: AI Search Optimizer
 * Plugin URI: https://github.com/gitrrm/ai-search-optimizer
 * Description: Optimize WordPress content for AI search engines and LLM discovery.
 * Version: 0.1.0
 * Author: Rashmi Ranjan Muduli
 * License: GPL-2.0-or-later
 * Text Domain: ai-search-optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASO_VERSION', '0.1.0' );
define( 'ASO_PLUGIN_FILE', __FILE__ );
define( 'ASO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ASO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ASO_PLUGIN_PATH . 'vendor/autoload.php';

use ASO\Core\Plugin;

Plugin::instance();