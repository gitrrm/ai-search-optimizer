<?php

namespace ASO\SEO;

if (! defined('ABSPATH')) {
	exit;
}

class RobotsDetector
{

	/**
	 * AI crawlers to check.
	 *
	 * @var array
	 */
	private $ai_crawlers = array(
		'GPTBot',
		'Google-Extended',
		'CCBot',
	);

	/**
	 * Analyze robots.txt.
	 *
	 * @return array
	 */
	public function analyze()
	{

		$response = wp_safe_remote_get(
			home_url('/robots.txt'),
			array(
				'timeout' => 8,
			)
		);

		$body        = '';
		$source      = 'url';
		$status_code = 0;

		if (! is_wp_error($response)) {
			$status_code = wp_remote_retrieve_response_code($response);
			$body        = wp_remote_retrieve_body($response);
		}

		/*
		 * WordPress can generate robots.txt virtually even when
		 * no physical robots.txt file exists.
		 */
		if (200 !== $status_code || empty(trim($body))) {

			$virtual_robots = $this->get_virtual_robots();

			if (! empty($virtual_robots)) {
				$body   = $virtual_robots;
				$source = 'wordpress';
			}
		}

		if (empty(trim($body))) {

			return array(
				'status'       => false,
				'available'    => false,
				'source'       => '',
				'ai_crawlers'  => array(),
				'has_ai_rules' => false,
				'error'        => 'robots.txt is not available.',
			);
		}

		$groups = $this->parse_robots_groups($body);

		$crawler_status = array();

		foreach ($this->ai_crawlers as $crawler) {

			$crawler_status[$crawler] = $this->analyze_crawler(
				$groups,
				$crawler
			);
		}

		$has_ai_rules = false;

		foreach ($crawler_status as $crawler) {

			if (! empty($crawler['specific_rule'])) {
				$has_ai_rules = true;
				break;
			}
		}

		return array(
			'status'       => true,
			'available'    => true,
			'source'       => $source,
			'ai_crawlers'  => $crawler_status,
			'has_ai_rules' => $has_ai_rules,
			'error'        => '',
		);
	}

	/**
	 * Backward-compatible boolean check.
	 *
	 * @return bool
	 */
	public function has_robots()
	{

		$result = $this->analyze();

		return ! empty($result['status']);
	}

	/**
	 * Get WordPress-generated robots.txt content.
	 *
	 * @return string
	 */
	private function get_virtual_robots()
	{

		/*
	 * WordPress builds virtual robots.txt content through
	 * the robots_txt filter. Use the filter directly instead
	 * of calling do_robots(), because do_robots() sends the
	 * response headers and outputs the content.
	 */
		$robots = apply_filters(
			'robots_txt',
			'',
			true
		);

		return is_string($robots) ? trim($robots) : '';
	}

	/**
	 * Parse robots.txt into user-agent groups.
	 *
	 * @param string $robots_txt Robots.txt content.
	 * @return array
	 */
	private function parse_robots_groups($robots_txt)
	{

		$lines  = preg_split('/\r\n|\r|\n/', $robots_txt);
		$groups = array();
		$agents = array();
		$rules  = array();

		foreach ($lines as $line) {

			$line = trim($line);

			if ('' === $line || 0 === strpos($line, '#')) {
				continue;
			}

			if (0 === stripos($line, 'User-agent:')) {

				$user_agent = trim(
					substr(
						$line,
						strlen('User-agent:')
					)
				);

				if (! empty($rules)) {

					$groups[] = array(
						'agents' => $agents,
						'rules'  => $rules,
					);

					$agents = array();
					$rules  = array();
				}

				if ('' !== $user_agent) {
					$agents[] = $user_agent;
				}

				continue;
			}

			if (
				0 === stripos($line, 'Disallow:') ||
				0 === stripos($line, 'Allow:')
			) {

				$parts = explode(':', $line, 2);

				$directive = strtolower(trim($parts[0]));
				$path      = isset($parts[1])
					? trim($parts[1])
					: '';

				$rules[] = array(
					'directive' => $directive,
					'path'      => $path,
				);
			}
		}

		if (! empty($agents)) {

			$groups[] = array(
				'agents' => $agents,
				'rules'  => $rules,
			);
		}

		return $groups;
	}

	/**
	 * Analyze rules applicable to a crawler.
	 *
	 * @param array  $groups  Parsed robots groups.
	 * @param string $crawler Crawler name.
	 * @return array
	 */
	private function analyze_crawler($groups, $crawler)
	{

		$specific_groups = array();
		$wildcard_groups = array();

		foreach ($groups as $group) {

			foreach ($group['agents'] as $agent) {

				if (0 === strcasecmp($agent, $crawler)) {
					$specific_groups[] = $group;
				}

				if ('*' === $agent) {
					$wildcard_groups[] = $group;
				}
			}
		}

		/*
		 * A crawler-specific group takes precedence over
		 * the wildcard group.
		 */
		$selected_groups = ! empty($specific_groups)
			? $specific_groups
			: $wildcard_groups;

		$disallow_all = false;
		$allow_root   = false;

		foreach ($selected_groups as $group) {

			foreach ($group['rules'] as $rule) {

				if (
					'disallow' === $rule['directive'] &&
					'/' === $rule['path']
				) {
					$disallow_all = true;
				}

				if (
					'allow' === $rule['directive'] &&
					'/' === $rule['path']
				) {
					$allow_root    = true;
					$disallow_all  = false;
				}
			}
		}

		return array(
			'found'         => ! empty($selected_groups),
			'specific_rule' => ! empty($specific_groups),
			'allowed'       => ! $disallow_all,
			'disallow_all'  => $disallow_all,
			'allow_root'    => $allow_root,
		);
	}
}
