async function getData(url = '', data = {}, csrfToken) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    });
    return response.json();
}
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);

    const data = { method: 'get' };
    const urlData = vURL + '/users-api';
    console.log('URL Data:', urlData);
    console.log('Data to send:', data);
    const usersData = getData(urlData, data, csrfToken);

    console.log(usersData);
});