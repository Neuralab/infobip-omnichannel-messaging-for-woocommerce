<?php
/**
 * Wrapper template for rendering plugin pages.
 *
 * Available variables:
 *
 *  $args['settings']      - \InfobipOmnichannel\Core\Settings instance
 *  $args['module']        - Specific module class instance if rendering module page
 *  $args['menu_pages']    - Menu page name to render
 *  $args['template_args'] - Args to pass to template
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;
?>
<div class="infobip-omnichannel-wrap">
	<div class="infobip-omnichannel-header">
		<?php Helper::render_svg( 'logo' ); ?>
	</div>

	<div class="infobip-omnichannel-content grid-sidebar">
		<?php
		if ( $args['module'] && [] !== $args['module']->nav ) {
			?>
			<div class="grid-sidebar__nav">
				<ul class="infobip-omnichannel-nav">
					<?php foreach ( $args['module']->nav as $nav_item_id => $nav_item_label ) { ?>
						<li>
							<a class="js-navigation-link <?php echo esc_attr( array_key_first( $args['module']->nav ) === $nav_item_id ? 'active' : '' ); ?>" href="<?php echo esc_url( Helper::get_menu_page_url( $args['module']->module_id, $nav_item_id ) ); ?>" id="<?php echo esc_attr( 'nav-' . $nav_item_id ); ?>" >
								<?php Helper::render_svg( 'icon-' . $nav_item_id ); ?>
								<?php echo esc_html( $nav_item_label ); ?>
							</a>
						</li>
					<?php	} ?>
				</ul>
			</div>
			<?php
		}
		?>
		<div class="grid-sidebar__content">
			<?php
			if ( $args['module'] ) {
				call_user_func( [ $args['module'], 'load_module_template' ], $args['template_args'] );
			} else {
				call_user_func( [ '\InfobipOmnichannel\Utility\Helper', 'load_template' ], $args['menu_page'] . '.php', [ 'settings' => $args['settings'] ] );
			}
			?>
		</div>
	</div>
</div>
