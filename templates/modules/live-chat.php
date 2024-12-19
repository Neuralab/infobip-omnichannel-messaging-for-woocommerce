<?php
/**
 * Module template - Live Chat
 *
 * Available variables:
 *
 *  $args['module'] - \InfobipOmnichannel\Modules\LiveChat instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.1
 */

use \InfobipOmnichannel\Utility\Helper;
?>

<h4><?php esc_html_e( 'Live Chat', 'infobip-omnichannel' ); ?></h4>

<div class="tabs">
	<section id="about" class="active">
		<div class="d-flex ai-center jc-between mb-3">
			<h1 class="mb-0"><?php esc_html_e( 'About', 'infobip-omnichannel' ); ?></h1>
			<form id="infobip-omnichannel-settings-enablers" action="options.php" method="post">
				<?php settings_fields( Helper::get_plugin_slug() ); ?>
				<?php Helper::load_template_part( 'switch-checkbox.php', [ 'module' => $args['module'] ] ); ?>
				<?php Helper::load_template_part( 'modal-live-chat.php', [ 'module' => $args['module'] ] ); ?>
				<input type="hidden" name="single_module_enabler" value="<?php echo esc_attr( $args['module']->module_id ); ?>">
			</form>
		</div>

		<h2><?php esc_html_e( 'Revolutionize Customer Support with Infobip Live Chat', 'infobip-omnichannel' ); ?></h2>

		<p class="mb-5">
			<?php esc_html_e( 'Infobip’s Live Chat module brings advanced customer engagement capabilities directly to your WooCommerce store. Designed for modern eCommerce, this tool enables businesses to interact with their customers in real time, offering exceptional support and fostering trust.', 'infobip-omnichannel' ); ?>
		</p>

		<img class="mb-5 mw-100" src="<?php echo esc_url( Helper::get_img_url( 'live-chat-image-1.jpg' ) ); ?>" alt="Infobip Live Chat widget example">

		<p class="fw-bold mb-2">
			<?php esc_html_e( 'Key Features (all managed through the Answers platform):', 'infobip-omnichannel' ); ?>
		</p>

		<ul class="list-styled mb-5">
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Rule-based chatbots', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Build useful chatbots that can provide immediate assistance to customers and guide them to the answers they need.', 'infobip-omnichannel' ); ?>
			</li>
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Intent-based chatbots', 'infobip-omnichannel' ); ?>: </span>
				<?php
				printf(
					// translators: links to infobip blog articles.
					esc_html__( 'Build a chatbot using %1$s and natural language processing to understand intents, process information, and replicate the normal conversational experience of interacting with a human agent. Quickly build a chatbot using our simple drag-and-drop interface and pre-defined templates. Use Generative AI modules, guardrails, %2$s and routers to enable a proper enterprise-grade chat experience.', 'infobip-omnichannel' ),
					'<a href="https://www.infobip.com/blog/generative-ai-for-customer-service" target="_blank">' . esc_html__( 'Generative AI', 'dys' ) . '</a>',
					'<a href="https://www.infobip.com/blog/infobip-splxai-partnership" target="_blank">' . esc_html__( 'automated red teaming', 'dys' ) . '</a>'
				);
				?>
			</li>
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Conversation / Chat handling', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Route users request to chatbots or human operators. You can even integrate a 3rd party support system and route conversations towards it.', 'infobip-omnichannel' ); ?>
			</li>
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Customizable Widgets', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Tailor the chat widget to match your brand identity and customer needs, with full control and insights via the People platform.', 'infobip-omnichannel' ); ?>
			</li>
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Pre-Chat Forms', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Collect vital customer information before initiating a conversation, enhancing lead generation and ensuring efficient support routing through People’s data mapping.', 'infobip-omnichannel' ); ?>
			</li>
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Multithreading', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Handle multiple conversations seamlessly, improving issue resolution and customer satisfaction, all tracked and stored in the Answers platform for future reference.', 'infobip-omnichannel' ); ?>
			</li>
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Integration-Ready', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Connect with platforms like Google Analytics, Tag Manager, and more, using the People platform to streamline data flow and insights.', 'infobip-omnichannel' ); ?>
			</li>
			<li>
				<span class="fw-bold"><?php esc_html_e( 'Personalization', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Authenticate users with JWT tokens and manage personalized experiences directly within the People platform.', 'infobip-omnichannel' ); ?>
			</li>
		</ul>

		<img class="mb-5 mw-100" src="<?php echo esc_url( Helper::get_img_url( 'live-chat-image-2.jpg' ) ); ?>" alt="Infobip Live Chat widget example">
	</section>

	<section id="settings">
		<h1 class="mb-3"><?php esc_html_e( 'Settings', 'infobip-omnichannel' ); ?></h1>
		<form id="infobip-omnichannel-settings" action="options.php" method="post">
			<?php settings_fields( Helper::get_plugin_slug( $args['module']->module_id ) ); ?>

			<p class="mb-3 fw-bold">
				<?php esc_html_e( 'Configure your Live Chat integration preferences for seamless customer support.', 'infobip-omnichannel' ); ?>
			</p>

			<p class="mb-3">
				<span class="fw-bold"><?php esc_html_e( 'Embed Code', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Input your Live Chat embed code into the textbox below to enable front-end display on your WooCommerce store.', 'infobip-omnichannel' ); ?>
			</p>

			<p class="mb-5">
				<span class="fw-bold"><?php esc_html_e( 'Customization', 'infobip-omnichannel' ); ?>: </span>
				<?php esc_html_e( 'Any additional customization or parameter adjustments to the embed code can be managed directly through the Infobip Answers platform.', 'infobip-omnichannel' ); ?>
			</p>

			<p class="mb-3 fw-bold">
				<?php esc_html_e( 'Create and Install the Web Widget', 'infobip-omnichannel' ); ?>:
			</p>

			<ul class="list-styled mb-5">
				<li>
					<?php
					printf(
						// translators: link to login into Infobip portal.
						esc_html__( '%s to your Infobip account, or create one if needed.', 'infobip-omnichannel' ),
						'<a href="https://portal.infobip.com/login/" target="_blank">' . esc_html__( 'Log in', 'dys' ) . '</a>'
					);
					?>
				</li>
				<li>
					<?php
					printf(
						// translators: link to live chat settings in Infobip portal.
						esc_html__( 'Navigate to %s.', 'infobip-omnichannel' ),
						'<a href="https://portal.infobip.com/apps/livechat/widgets" target="_blank">' . esc_html__( 'Channels and Numbers → Live Chat → Create Widget', 'dys' ) . '</a>'
					);
					?>
				</li>
				<li>
					<?php
					printf(
						// translators: bold style on Installation & Security.
						esc_html__( 'Copy the widget installation code snippet from the %s section', 'infobip-omnichannel' ),
						'<span class="fw-bold">' . esc_html__( 'Installation & Security', 'dys' ) . '</span>'
					);
					?>
				</li>
				<li>
					<?php
					printf(
						// translators: bold style on embed code field text.
						esc_html__( 'Enter the code snippet into the %s (textbox below).', 'infobip-omnichannel' ),
						'<span class="fw-bold">' . esc_html__( 'embed code field of the plugin', 'dys' ) . '</span>'
					);
					?>
				</li>
			</ul>

			<p class="mb-2">
				<?php
				printf(
					// translators: link to live chat documentation.
					esc_html__( 'For more detailed guidance, refer to the %s documentation.', 'infobip-omnichannel' ),
					'<a href="https://www.infobip.com/docs/live-chat/getting-started" target="_blank">' . esc_html__( 'Getting Started with Live Chat', 'dys' ) . '</a>'
				);
				?>
			</p>

			<p class="mb-5">
				<?php esc_html_e( 'Paste your Live Chat embed code here:', 'infobip-omnichannel' ); ?>
			</p>

			<div class="form-field form-field--code">
				<label for="infobip-omnichannel-live-chat-code" class="form-label"><?php esc_html_e( 'Embed code', 'infobip-omnichannel' ); ?></label>
				<textarea id="infobip-omnichannel-live-chat-code" class="form-control" name="<?php echo esc_attr( $args['module']->module_input_name( 'live_chat_code' ) ); ?>" rows="9" cols="62" spellcheck="false" data-required="true"><?php echo $args['module']->get_module_setting( 'live_chat_code' ); //@codingStandardsIgnoreLine - no escaping needed. ?></textarea>
			</div>

			<input type="submit" name="submit" class="btn btn--primary" value="<?php esc_attr_e( 'Save settings', 'infobip-omnichannel' ); ?>" />

		</form>
	</section>
</div>
