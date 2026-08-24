<?php

namespace ASO\Core;

use ASO\Admin\Admin;
use ASO\SEO\FaqSchemaGenerator;

if (! defined('ABSPATH')) {
	exit;
}

class Plugin
{

	/**
	 * Plugin instance
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get instance
	 */
	public static function instance()
	{

		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct()
	{
		$this->init();
	}

	/**
	 * Initialize plugin
	 */
	private function init()
	{

		add_action(
			'init',
			array($this, 'load_plugin')
		);
	}

	/**
	 * Load plugin services
	 */
	public function load_plugin()
	{

		if (is_admin()) {
			new Admin();
		}

		$faq = new FaqSchemaGenerator();

		$faq->init();

		do_action(
			'aso_loaded'
		);
	}
}
