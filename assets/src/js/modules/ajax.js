import { remotePost } from '../utilities/request';
import { renderAlert, clearAlerts, isValidated, clearValidationMessages } from '../utilities/helper';

const { __ } = wp.i18n;

const form = document.getElementById('ajax-form');
if ( form ) {
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    clearValidationMessages();
    if (! isValidated(form)) {
      return false;
    }

    var submitButton = e.target.querySelector('[type="submit"]');

    if (submitButton) {
      submitButton.classList.add('loading');
    }

    clearAlerts();

    return remotePost(
      main_data.ajax_url,
      new FormData(e.target),
    ).then(response => {
      if ( response.success ) {
        if (response.data && response.data.success ) {
          renderAlert( form, 'success', response.data.success );
        }

        if ( response.data && response.data.info ) {
          renderAlert( form, 'info', response.data.info );
        }
      } else {
        if ( response.data && response.data.error ) {
          renderAlert( form, 'error', response.data.error );
        }
      }

      if (submitButton) {
        submitButton.classList.remove('loading');
      }
    });

  });
}

const smsBtn = document.getElementById('infobip-message-button');
if ( smsBtn ) {
  smsBtn.addEventListener('click', (e) => {
    e.preventDefault();

    var body = new FormData(document.getElementById('order'));
    if ( ! body.get('infobip_sms_message') ) {
      return;
    }

    body.set('action', 'infobip_omnichannel_sms_send');

    smsBtn.classList.add('loading');

    return remotePost(
      main_data.ajax_url,
      body,
    ).then(response => {
      smsBtn.classList.remove('loading');

      if (response.success) {
        alert(__('Message successfully sent!', 'infobip-omnichannel'));
        document.getElementById('infobip-message-text').value = '';
      } else {
        alert(__('Failed to send the message, please check Infobip Omnichannel Messaging for WooCommerce logs and settings.', 'infobip-omnichannel'));
      }
    });

  });
}

