import { saveOptions } from '../utilities/request';
import { toggleModal, renderAlert, clearAlerts, isValidated, clearValidationMessages } from '../utilities/helper';

const { __ } = wp.i18n;

const enablersForm = document.querySelector('#infobip-omnichannel-settings-enablers');
if ( enablersForm ) {
  var enablers = enablersForm.querySelectorAll('.enable-checkbox');

  if ( enablers ) {
    enablers.forEach(enabler => {
      enabler.addEventListener('change', (e) => {
        const enablerSwitch = e.target.closest('.switch');
        const modal         = document.getElementById(e.target.dataset.modal);

        enablerSwitch.classList.add('loading');

        if (! e.target.checked) {
          saveOptions(enablersForm).then(() => {
            enablerSwitch.classList.remove('loading');
          });
        } else {
          toggleModal(modal, e.target.checked);
        }
      });
    });
  }
}

const forms = document.querySelectorAll('.infobip-omnichannel-wrap form[action="options.php"]');
if ( forms ) {
  forms.forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      clearValidationMessages();
      if ( ! isValidated(form) ) {
        return false;
      }

      var submitButton = e.target.querySelector('[type="submit"]');
      if (submitButton) {
        submitButton.classList.add('loading')
      }

      clearAlerts();

      saveOptions( e.target ).then(response => {
        if ( 'infobip-omnichannel-settings-enablers' === form.id && enablers ) {
          enablers.forEach(enabler => {
            const enablerSwitch = enabler.closest('.switch');
            enablerSwitch.classList.remove('loading');
          });
        } else {
          if ( response.success ) {
            if ( enablersForm ) {
              const modal = enablersForm.querySelector('.modal');
              if ( modal ) {
                modal.classList.add('activate');
              }
            }

            renderAlert( form, 'success', __( 'Settings saved successfully!', 'infobip-omnichannel' ) );

          } else {
            if ( response.data && response.data.error ) {
              renderAlert( form, 'error', response.data.error );
            }
          }
        }

        if (submitButton) {
          submitButton.classList.remove('loading')
        }
      });
    });
  });
}

