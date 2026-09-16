<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$score      = isset( $data['ai_score'] ) ? (int) $data['ai_score'] : 0;
$max_score  = isset( $data['max_score'] ) ? (int) $data['max_score'] : 100;
$checks     = isset( $data['checks'] ) ? $data['checks'] : array();
$results    = isset( $data['results'] ) ? $data['results'] : array();
$recommendations = isset( $data['recommendations'] ) ? $data['recommendations'] : array();
?>

<div class="wrap">
	<h1><?php esc_html_e( 'AI Search Optimizer', 'ai-search-optimizer' ); ?></h1>
	<div class="aso-dashboard">
		<div class="aso-score-card">
			<h2><?php esc_html_e( 'AI Visibility Score', 'ai-search-optimizer' ); ?></h2>
			<div class="aso-score">
				<?php echo esc_html( $score ); ?>
				<span>/ <?php echo esc_html( $max_score ); ?></span>
			</div>
			<p><?php esc_html_e( 'This is a heuristic score based on the checks performed by the plugin.', 'ai-search-optimizer' ); ?></p>
		</div>

		<div class="aso-checks-card">
			<h2><?php esc_html_e( 'Optimization Checks', 'ai-search-optimizer' ); ?></h2>
			<table class="widefat striped">
				<thead><tr><th><?php esc_html_e( 'Check', 'ai-search-optimizer' ); ?></th><th><?php esc_html_e( 'Status', 'ai-search-optimizer' ); ?></th><th><?php esc_html_e( 'Score', 'ai-search-optimizer' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( $checks as $key => $check ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $check['label'] ); ?></strong></td>
						<td><?php if ( ! empty( $check['passed'] ) ) : ?><span class="aso-status aso-status-pass"><?php esc_html_e( 'Passed', 'ai-search-optimizer' ); ?></span><?php else : ?><span class="aso-status aso-status-fail"><?php esc_html_e( 'Needs attention', 'ai-search-optimizer' ); ?></span><?php endif; ?></td>
						<td><?php echo esc_html( $check['earned'] . ' / ' . $check['weight'] ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if ( ! empty( $recommendations ) ) : ?>
		<div class="aso-recommendations-card">
			<h2><?php esc_html_e( 'Recommendations', 'ai-search-optimizer' ); ?></h2>
			<p><?php esc_html_e( 'Actions you can consider to improve the signals evaluated by this plugin.', 'ai-search-optimizer' ); ?></p>
			<ul class="aso-recommendations">
			<?php foreach ( $recommendations as $recommendation ) : ?>
				<li>
					<strong><?php echo esc_html( $recommendation['title'] ); ?></strong>
					<?php if ( ! empty( $recommendation['priority'] ) ) : ?><span class="aso-priority"><?php echo esc_html( ucfirst( $recommendation['priority'] ) ); ?></span><?php endif; ?>
					<p><?php echo esc_html( $recommendation['message'] ); ?></p>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<?php if ( isset( $results['schema'] ) ) : ?>
		<div class="aso-detail-card"><h2><?php esc_html_e( 'Schema Details', 'ai-search-optimizer' ); ?></h2>
		<?php if ( ! empty( $results['schema']['status'] ) ) : ?><p><strong><?php esc_html_e( 'Schema detected:', 'ai-search-optimizer' ); ?></strong> <?php echo esc_html( $results['schema']['count'] ); ?></p><?php if ( ! empty( $results['schema']['types'] ) ) : ?><p><strong><?php esc_html_e( 'Types:', 'ai-search-optimizer' ); ?></strong> <?php echo esc_html( implode( ', ', $results['schema']['types'] ) ); ?></p><?php endif; ?><?php else : ?><p><?php esc_html_e( 'No Schema.org JSON-LD types were detected on the homepage.', 'ai-search-optimizer' ); ?></p><?php endif; ?></div>
		<?php endif; ?>

		<?php if ( isset( $results['opengraph'] ) ) : ?>
		<div class="aso-detail-card"><h2><?php esc_html_e( 'Open Graph Details', 'ai-search-optimizer' ); ?></h2>
		<?php if ( ! empty( $results['opengraph']['tags'] ) ) : ?><ul><?php foreach ( $results['opengraph']['tags'] as $tag => $value ) : ?><li><strong><?php echo esc_html( $tag ); ?>:</strong> <?php echo esc_html( $value ); ?></li><?php endforeach; ?></ul><?php endif; ?>
		<?php if ( ! empty( $results['opengraph']['missing'] ) ) : ?><p><strong><?php esc_html_e( 'Missing:', 'ai-search-optimizer' ); ?></strong> <?php echo esc_html( implode( ', ', $results['opengraph']['missing'] ) ); ?></p><?php endif; ?></div>
		<?php endif; ?>

		<?php if ( isset( $results['robots'] ) ) : ?>
		<div class="aso-detail-card"><h2><?php esc_html_e( 'AI Crawler Access', 'ai-search-optimizer' ); ?></h2>
		<?php if ( ! empty( $results['robots']['available'] ) ) : ?><p><?php esc_html_e( 'robots.txt is available.', 'ai-search-optimizer' ); ?></p><?php if ( ! empty( $results['robots']['ai_crawlers'] ) ) : ?><table class="widefat striped"><thead><tr><th><?php esc_html_e( 'Crawler', 'ai-search-optimizer' ); ?></th><th><?php esc_html_e( 'Status', 'ai-search-optimizer' ); ?></th></tr></thead><tbody><?php foreach ( $results['robots']['ai_crawlers'] as $crawler => $crawler_data ) : ?><tr><td><?php echo esc_html( $crawler ); ?></td><td><?php if ( ! empty( $crawler_data['disallow_all'] ) ) : ?><span class="aso-status aso-status-fail"><?php esc_html_e( 'Blocked', 'ai-search-optimizer' ); ?></span><?php elseif ( ! empty( $crawler_data['found'] ) ) : ?><span class="aso-status aso-status-pass"><?php esc_html_e( 'Allowed', 'ai-search-optimizer' ); ?></span><?php else : ?><span class="aso-status"><?php esc_html_e( 'No specific rule', 'ai-search-optimizer' ); ?></span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?><?php else : ?><p><?php esc_html_e( 'robots.txt could not be detected.', 'ai-search-optimizer' ); ?></p><?php endif; ?></div>
		<?php endif; ?>

		<?php if ( isset( $results['llms'] ) ) : ?><div class="aso-detail-card"><h2><?php esc_html_e( 'llms.txt', 'ai-search-optimizer' ); ?></h2><?php if ( ! empty( $results['llms']['status'] ) ) : ?><p class="aso-status aso-status-pass"><?php esc_html_e( 'llms.txt detected and accessible.', 'ai-search-optimizer' ); ?></p><?php else : ?><p class="aso-status aso-status-fail"><?php esc_html_e( 'llms.txt was not detected.', 'ai-search-optimizer' ); ?></p><?php endif; ?></div><?php endif; ?>

		<?php if ( isset( $results['faq'] ) ) : ?><div class="aso-detail-card"><h2><?php esc_html_e( 'FAQ Content', 'ai-search-optimizer' ); ?></h2><?php if ( ! empty( $results['faq']['status'] ) ) : ?><p class="aso-status aso-status-pass"><?php esc_html_e( 'FAQ content detected.', 'ai-search-optimizer' ); ?></p><?php if ( ! empty( $results['faq']['faq_count'] ) ) : ?><p><strong><?php esc_html_e( 'FAQ items:', 'ai-search-optimizer' ); ?></strong> <?php echo esc_html( $results['faq']['faq_count'] ); ?></p><?php endif; ?><?php else : ?><p class="aso-status aso-status-fail"><?php esc_html_e( 'No FAQ content was detected on the front page.', 'ai-search-optimizer' ); ?></p><?php endif; ?></div><?php endif; ?>
	</div>
</div>
