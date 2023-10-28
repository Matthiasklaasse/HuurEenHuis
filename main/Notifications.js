function MakeNotification(Text) {
    var div = document.createElement("div");
    div.classList.add("Notification");
    div.innerHTML = Text;
    NotificationsBar.appendChild(div);

    setTimeout(function () {
        div.classList.add("active");
        setTimeout(function () {
            div.remove();
        }, 1000);
    }, 10000);

    div.addEventListener('click', function () {
        div.classList.add("active");
        setTimeout(function () {
            div.remove();
        }, 100);
    });
}

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('notification')) {
    const notificationText = decodeURIComponent(urlParams.get('notification'));
    MakeNotification(notificationText);
    
    urlParams.delete('notification');
    const newURL = `${window.location.pathname}?${urlParams.toString()}`;
    history.replaceState({}, document.title, newURL);
}
