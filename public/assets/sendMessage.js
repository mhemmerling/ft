document.getElementById('send-message').addEventListener('submit', event => {
    event.preventDefault();

    fetch('/api/v1/messages', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'title': document.getElementById('title-input').value,
            'content': document.getElementById('content-input').value,
            'recipients': Array.from(document.getElementById('recipients-input').selectedOptions).map(option => option.value),
        })}
    ).then(response => {
        if (response.status === 202) {
            location.reload();
        } else {
            alert('Error');
        }
    }).catch(error => {
        alert('Error: ' + error);
    });
});