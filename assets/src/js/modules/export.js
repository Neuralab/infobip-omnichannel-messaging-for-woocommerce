import { remotePost } from '../utilities/request';
import { renderAlert, clearAlerts } from '../utilities/helper';

const exportForm = document.getElementById('export-form');
if ( exportForm ) {
	var progressBar        = document.getElementById('export-progress-bar');
  var progressPercentage = document.getElementById('export-progress-percent');
	var maxPages    = 0;

	var usersExport = [
    Object.values(main_data.user_data),
	];

  exportForm.addEventListener('submit', (e) => {
    e.preventDefault();

    fetchUsers( 1, e );
  });
}

function fetchUsers( page, event ) {
  const body           = new FormData(event.target);
  const downloadLink   = document.getElementById('export-download');
  const exportProgress = document.getElementById('export-progress');
  downloadLink.classList.replace( 'd-inline-block', 'd-none' );
  exportProgress.classList.replace( 'd-none', 'd-block' );
  progressPercentage.innerHTML = '0%';
  progressBar.style.width = '0';

  clearAlerts();

  body.set( 'page', page );

  var submitButton = event.target.querySelector('[type="submit"]');

  if (submitButton) {
    submitButton.classList.add('loading');
  }

  return remotePost(
    main_data.ajax_url,
    body,
  ).then(response => {
    if (response.success) {
      maxPages = Math.ceil( response.data.total / 10 );
      if ( maxPages >= page ) {
        fetchUsers( page + 1, event );
        progressPercentage.innerHTML = ( (100 / maxPages ) * page ) + '%';
        progressBar.style.width = ((100 / maxPages ) * page) + '%';
        usersExport = usersExport.concat(response.data.users);
      } else {
        var csvContent = 'data:text/csv;charset=utf-8,' + usersExport.map(val => val.join(',')).join('\n');
        var encodedUri = encodeURI(csvContent);

        downloadLink.setAttribute('href', encodedUri);
        downloadLink.setAttribute('download', 'users-export-' + new Date().toJSON().slice(0, 10) + '.csv');
        downloadLink.classList.replace( 'd-none', 'd-inline-block' );

        if (submitButton) {
          submitButton.classList.remove('loading');
        }
      }
    } else {
      if ( response.data.error ) {
        exportProgress.classList.replace( 'd-block', 'd-none' );
        renderAlert( exportForm, 'error', response.data.error );
      }

      if (submitButton) {
        submitButton.classList.remove('loading');
      }
    }

  });
}
