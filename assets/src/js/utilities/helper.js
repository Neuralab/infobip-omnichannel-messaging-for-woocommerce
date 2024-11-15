const { __ } = wp.i18n;

export function toggleModal(modal, action = null) {
  modal.classList.toggle('visible', action === null ? ! modal.classList.contains('visible') : action);
}

export function renderAlert(target, type, message) {
  const alert = document.createElement('div');
  alert.classList = 'js-alert-response alert alert--'+type;
  alert.innerHTML = message;
  target.appendChild(alert);
}

export function clearAlerts() {
  document.querySelectorAll('.js-alert-response').forEach(e => e.remove());
}

export function clearValidationMessages() {
  document.querySelectorAll('.js-invalid-message').forEach(e => e.remove());
}

export function isValidated(form) {
  let valid = true;
  const requiredFields = form.querySelectorAll('[data-required="true"]');

  if ( requiredFields ) {
    requiredFields.forEach(field => {
      if ( ! field.value ) {
        field.classList.add( 'form-control--invalid' );
        field.insertAdjacentHTML('afterend', '<small class="js-invalid-message invalid-message">' + __( 'This field is required', 'infobip-omnichannel' ) + '</small>');
      } else {
        field.classList.remove( 'form-control--invalid' );
      }

      if ( ! field.value) {
        valid = false;
      }
    });
  }

  return valid;
}
