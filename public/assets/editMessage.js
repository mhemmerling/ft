document.getElementById('edit-message').addEventListener('submit', event => {
    event.preventDefault();

    console.log('sdsadss');

    fetch('/api/v1/messages/' + document.getElementById('id-input').value, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            title: document.getElementById('title-input').value,
            content: document.getElementById('content-input').value
        }),
    }).then(response => {
        if (response.status === 202) {
            window.location.href = '/messages/sent';
        } else {
            alert('Error');
        }
    }).catch(error => {
        alert('Error: ' + error);
    });
});