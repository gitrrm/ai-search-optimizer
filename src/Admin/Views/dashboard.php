<div class="wrap">

	<h1>AI Search Optimizer</h1>

	<div class="card">

		<h2>AI Visibility Score</h2>

		<h1>
			<?php echo esc_html($data['ai_score']); ?>/100
		</h1>

	</div>

	<div class="card">

		<h2>Checks</h2>

		<table class="widefat">

			<thead>

				<tr>

					<th>
						Check
					</th>

					<th>
						Status
					</th>

					<th>
						Score
					</th>

				</tr>

			</thead>

			<tbody>

				<?php foreach (
					$data['checks']
					as $name => $check
				) : ?>

					<tr>

						<td>

							<?php echo esc_html(
								ucfirst(
									$name
								)
							); ?>

						</td>

						<td>

							<?php echo
							$check['status']
								? '✅'
								: '❌'; ?>

						</td>

						<td>

							+<?php echo esc_html(
									$check['weight']
								); ?>

						</td>

					</tr>

				<?php endforeach; ?>

			</tbody>

		</table>

	</div>

</div>