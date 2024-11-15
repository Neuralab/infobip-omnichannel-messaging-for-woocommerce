import { toggleModal } from '../utilities/helper';

const triggersOpen   = document.querySelectorAll('.iomni-modal-open'),
      triggersClose  = document.querySelectorAll('.iomni-modal-close'),
      triggersCancel = document.querySelectorAll('.iomni-modal-cancel');

triggersOpen.forEach(trigger => {
  const modal = document.getElementById(trigger.dataset.modal);
  trigger.addEventListener('click', () => toggleModal(modal, true));
});

triggersClose.forEach(trigger => {
  const modal = document.getElementById(trigger.dataset.modal);
  trigger.addEventListener('click', () => toggleModal(modal, false));
});

triggersCancel.forEach(trigger => {
  const modal = document.getElementById(trigger.dataset.modal);
  trigger.addEventListener('click', () => {
    const enabler       = document.querySelector('[data-modal="'+trigger.dataset.modal+'"]');
    const enablerSwitch = enabler.closest('.switch');
    toggleModal(modal, false);
    enabler.checked = false;
    enablerSwitch.classList.remove('loading');
  });
});

