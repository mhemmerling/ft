function deleteWholeMessage(id) {
    fetch('/api/v1/messages/' + id + '/full', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.status === 202) {
            location.reload();
        } else {
            alert('Error');
        }
    }).catch(error => {
        alert('Error: ' + error);
    });
}