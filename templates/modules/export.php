<?php
/**
 * Module template - Export
 *
 * Available variables:
 *
 *  $args['module'] - \InfobipOmnichannel\Modules\Export instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */
?>

<h4><?php esc_html_e( 'Export', 'infobip-omnichannel' ); ?></h4>

<div class="tabs">
	<section id="about" class="active">
		<h1><?php esc_html_e( 'About', 'infobip-omnichannel' ); ?></h1>
		<p class="mb-5">
			<?php esc_html_e( 'Easily download your WooCommerce customer data in CSV format. You can import customer data into the People module in the Infobip portal.', 'infobip-omnichannel' ); ?>
		</p>

		<p>
			<?php
			printf(
				// translators: Info about importing file to Infobip.
				esc_html__( 'You can find information on how to import the exported file in %s.', 'infobip-omnichannel' ),
				'<a href="https://www.infobip.com/docs/people/synchronize-your-audience#import" target="_blank">' . esc_html__( 'Infobip Import documentation', 'dys' ) . '</a>'
			);
			?>
		</p>
	</section>

	<section id="export">
		<h1><?php esc_html_e( 'Export', 'infobip-omnichannel' ); ?></h1>
		<p class="mb-5">
			<?php esc_html_e( 'WooCommerce export data will include these customer data points: ', 'infobip-omnichannel' ); ?>
		</p>
		<ul class="list-styled">
			<?php foreach ( $args['module']->export_fields as $field ) { ?>
				<li><?php echo esc_html( $field ); ?></li>
			<?php } ?>
		</ul>

		<form id="export-form" class="nb-export">
			<?php wp_nonce_field( $args['module']->export_action_id, 'export_form' ); ?>
			<input type="hidden" name="action" value="<?php echo esc_attr( $args['module']->export_action_id ); ?>">

			<input type="submit" name="test_email" class="btn btn--primary mb-5" value="<?php esc_attr_e( 'Export CSV File', 'infobip-omnichannel' ); ?>" />

			<div id="export-progress" class="d-none">
				<p class="mb-3">
					<b><?php esc_html_e( 'Export in progress...', 'infobip-omnichannel' ); ?></b>
				</p>

				<div class="media-item">
					<div class="progress">
						<div id="export-progress-bar" class="bar"></div>
					</div>
					<div class="my-2">
						<?php
						printf(
							// translators: How much of the export progress is underway in percentages.
							esc_html__( '%s completed', 'infobip-omnichannel' ),
							'<span id="export-progress-percent" class="percent">0%</span>'
						);
						?>
					</div>
				</div>
			</div>

			<a id="export-download" class="btn btn--secondary d-none" href="#"><?php esc_html_e( 'Download', 'infobip-omnichannel' ); ?></a>
		</form>
	</section>
</div>
