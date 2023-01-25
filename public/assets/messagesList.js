function setAsRead(id) {
    let xhr = new XMLHttpRequest();
    xhr.open('PUT', '/api/v1/messages/' + id + '/read', true);
    xhr.send();

    xhr.onreadystatechange = (e) => {
        location.reload();
    }
}

    function setAsUnread(id) {
    let xhr = new XMLHttpRequest();
    xhr.open('PUT', '/api/v1/messages/' + id + '/unread', true);
    xhr.send();

    xhr.onreadystatechange = (e) => {
        location.reload();
    }
}

    function deleteMessage(id) {
    let xhr = new XMLHttpRequest();
    xhr.open('DELETE', '/api/v1/messages/' + id, true);
    xhr.send();

    xhr.onreadystatechange = (e) => {
        location.reload();
    }
}