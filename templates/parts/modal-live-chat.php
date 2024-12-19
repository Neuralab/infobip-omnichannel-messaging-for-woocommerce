<?php
/**
 * LiveChat settings need setup modal template
 *
 * Available variables:
 *
 * $args['module] - \InfobipOmnichannel\Core\LiveChat instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.1
 */

use \InfobipOmnichannel\Utility\Helper;

$modal_id = 'modal-' . $args['module']->module_id;
?>

<div id="<?php echo esc_attr( $modal_id ); ?>" class="modal <?php echo esc_attr( $args['module']->is_setup_needed() ? '' : 'activate' ); ?>">
	<span class="modal-backdrop iomni-modal-cancel" data-modal="<?php echo esc_attr( $modal_id ); ?>"></span>
	<div class="modal-content">
		<h1 class="modal-title"><?php esc_html_e( 'Are you shure you want to turn on Live Chat?', 'infobip-omnichannel' ); ?></h1>
		<a href="#" class="iomni-modal-cancel iomni-modal-close-icon" data-modal="<?php echo esc_attr( $modal_id ); ?>">
			<?php Helper::render_svg( 'icon-close' ); ?>
		</a>
		<div class="modal-body">
			<p>
				<?php esc_html_e( 'Enabling Live Chat allows your application to showcase Live Chat widget to users.', 'infobip-omnichannel' ); ?>
			</p>
			<p>
				<?php
				printf(
					// translators: Infobip live chat documentation link.
					esc_html__( 'For more information go to %s', 'infobip-omnichannel' ),
					'<a href="https://www.infobip.com/docs/live-chat/getting-started" target="_blank">' . esc_html__( 'Live Chat Documentation', 'infobip-omnichannel' ) . '</a>'
				);
				?>
			</p>

			<div id="alert-activate" class="alert alert--info">
				<?php
				printf(
					// translators: Warning message for missing Live Chat embed code.
					esc_html__( 'Live Chat embed code missing, please go to %s and setup a Live Chat widget.', 'infobip-omnichannel' ),
					'<a href="' . esc_url( Helper::get_menu_page_url( 'live-chat', 'settings' ) ) . '">' . esc_html__( 'Live Chat settings', 'infobip-omnichannel' ) . '</a>'
				);
				?>
			</div>

		</div>
		<div class="modal-footer d-flex g-2 mt-3">
			<button type="button" class="iomni-modal-cancel btn btn--secondary btn--block" data-modal="<?php echo esc_attr( $modal_id ); ?>"><?php esc_html_e( 'Cancel', 'infobip-omnichannel' ); ?></button>
			<button id="btn-activate" type="submit" class="iomni-modal-close btn btn--primary btn--block" data-modal="<?php echo esc_attr( $modal_id ); ?>"><?php esc_html_e( 'Turn on', 'infobip-omnichannel' ); ?></button>
		</div>
	</div>
</div>
