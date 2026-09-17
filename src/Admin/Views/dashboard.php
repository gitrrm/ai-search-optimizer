<?php

if (! defined('ABSPATH')) {
	exit;
}

$score      = isset($data['ai_score']) ? (int) $data['ai_score'] : 0;
$max_score  = isset($data['max_score']) ? (int) $data['max_score'] : 100;
$checks     = isset($data['checks']) ? $data['checks'] : array();
$results    = isset($data['results']) ? $data['results'] : array();
$recommendations = isset($data['recommendations']) ? $data['recommendations'] : array();
$analysis_status = isset($_GET['aso_analysis'])
	? sanitize_key(wp_unslash($_GET['aso_analysis']))
	: '';
?>

<div class="wrap">
	<?php if ('success' === $analysis_status) : ?>

		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				esc_html_e(
					'Analysis refreshed successfully.',
					'ai-search-optimizer'
				);
				?>
			</p>
		</div>

	<?php endif; ?>

	<h1><?php esc_html_e('AI Search Optimizer', 'ai-search-optimizer'); ?></h1>

	<div class="aso-dashboard">

		<div class="aso-score-card">

			<h2><?php esc_html_e('AI Visibility Score', 'ai-search-optimizer'); ?></h2>

			<form
				method="post"
				action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
				<input
					type="hidden"
					name="action"
					value="aso_run_analysis" />

				<?php wp_nonce_field('aso_run_analysis'); ?>

				<?php submit_button(
					__('Run Analysis', 'ai-search-optimizer'),
					'secondary',
					'submit',
					false
				); ?>
			</form>

			<div class="aso-score">
				<?php echo esc_html($score); ?>
				<span>/ <?php echo esc_html($max_score); ?></span>
			</div>

			<p>
				<?php
				esc_html_e(
					'This is a heuristic score based on the checks performed by the plugin.',
					'ai-search-optimizer'
				);
				?>
			</p>

		</div>

		<div class="aso-checks-card">

			<h2><?php esc_html_e('Optimization Checks', 'ai-search-optimizer'); ?></h2>

			<table class="widefat striped">

				<thead>
					<tr>
						<th><?php esc_html_e('Check', 'ai-search-optimizer'); ?></th>
						<th><?php esc_html_e('Status', 'ai-search-optimizer'); ?></th>
						<th><?php esc_html_e('Score', 'ai-search-optimizer'); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php foreach ($checks as $key => $check) : ?>

						<tr>

							<td>
								<strong>
									<?php echo esc_html($check['label']); ?>
								</strong>
							</td>

							<td>

								<?php if (! empty($check['passed'])) : ?>

									<span class="aso-status aso-status-pass">
										<?php esc_html_e('Passed', 'ai-search-optimizer'); ?>
									</span>

								<?php else : ?>

									<span class="aso-status aso-status-fail">
										<?php esc_html_e('Needs attention', 'ai-search-optimizer'); ?>
									</span>

								<?php endif; ?>

							</td>

							<td>
								<strong>
									<?php
									echo esc_html(
										$check['earned'] . ' / ' . $check['weight']
									);
									?>
								</strong>
							</td>

						</tr>

					<?php endforeach; ?>

				</tbody>

			</table>
			<?php if (! empty($checks)) : ?>

				<div class="aso-check-details">

					<h3>
						<?php esc_html_e('Check Details', 'ai-search-optimizer'); ?>
					</h3>

					<?php foreach ($checks as $check) : ?>

						<div class="aso-check-detail">

							<h4>
								<?php echo esc_html($check['label']); ?>
							</h4>

							<?php if (! empty($check['details'])) : ?>

								<ul>

									<?php foreach ($check['details'] as $detail) : ?>

										<li>
											<?php echo esc_html($detail); ?>
										</li>

									<?php endforeach; ?>

								</ul>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>



		<?php if (! empty($recommendations)) : ?>

			<div class="aso-recommendations-card">

				<h2>
					<?php esc_html_e('Recommendations', 'ai-search-optimizer'); ?>
				</h2>

				<p>
					<?php
					esc_html_e(
						'Actions you can consider to improve the signals evaluated by this plugin.',
						'ai-search-optimizer'
					);
					?>
				</p>

				<ul class="aso-recommendations">

					<?php foreach ($recommendations as $recommendation) : ?>

						<li>

							<strong>
								<?php echo esc_html($recommendation['title']); ?>
							</strong>

							<?php if (! empty($recommendation['priority'])) : ?>

								<span class="aso-priority">
									<?php echo esc_html(ucfirst($recommendation['priority'])); ?>
								</span>

							<?php endif; ?>

							<p>
								<?php echo esc_html($recommendation['message']); ?>
							</p>

						</li>

					<?php endforeach; ?>

				</ul>

			</div>

		<?php endif; ?>

	</div>

</div>