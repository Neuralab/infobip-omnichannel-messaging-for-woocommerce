<?php
/**
 * Email settings need setup modal template
 *
 * Available variables:
 *
 * $args['module] - \InfobipOmnichannel\Core\Email instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;

$modal_id = 'modal-' . $args['module']->module_id;
?>

<div id="<?php echo esc_attr( $modal_id ); ?>" class="modal <?php echo esc_attr( $args['module']->is_setup_needed() ? '' : 'activate' ); ?>">
	<span class="modal-backdrop iomni-modal-cancel" data-modal="<?php echo esc_attr( $modal_id ); ?>"></span>
	<div class="modal-content">
		<h1 class="modal-title"><?php esc_html_e( 'Are you sure you want to turn on Email sending.', 'infobip-omnichannel' ); ?></h1>
		<a href="#" class="iomni-modal-cancel iomni-modal-close-icon" data-modal="<?php echo esc_attr( $modal_id ); ?>">
			<?php Helper::render_svg( 'icon-close' ); ?>
		</a>
		<div class="modal-body">
			<p>
				<?php esc_html_e( 'Enabling email sending allows your application to send emails to users.', 'infobip-omnichannel' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Before using email sending, you should verify domain on Infobip portal.', 'infobip-omnichannel' ); ?>
			</p>
			<div id="alert-activate" class="alert alert--info">
				<?php
				printf(
					// translators: Warning message for missing sender email address.
					esc_html__( 'Email sender address configuration missing please visit %s and setup an email address with validated domain.', 'infobip-omnichannel' ),
					'<a href="' . esc_url( Helper::get_menu_page_url( 'email', 'settings' ) ) . '">' . esc_html__( 'Email settings', 'infobip-omnichannel' ) . '</a>'
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
