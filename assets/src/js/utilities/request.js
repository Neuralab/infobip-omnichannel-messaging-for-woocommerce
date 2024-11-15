export async function remotePost( url, data ) {
  try {
    const response = await fetch(url, {
      method: 'POST',
      body: data,
    });

    const contentType = response.headers.get('content-type');
    if (contentType && contentType.indexOf('application/json') !== -1) {
      return response.json();
    }

    return new Promise(resolve => {
      resolve({success: true, data: null});
    });
  } catch (error) {
    console.error('Request failed due to following errors: ', error);
  }

  return new Promise(resolve => {
    resolve({success: false, data: null});
  });
}

export async function saveOptions( options ) {
  return remotePost( 'options.php', new FormData( options ) );
}
