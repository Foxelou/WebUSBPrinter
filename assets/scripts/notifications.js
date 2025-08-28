function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notification-container');
    const notif = document.createElement('div');
    notif.className = `notification ${type}`;
    notif.innerText = message;

    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar';
    progressBar.style.animationDuration = `${duration}ms`;
    notif.appendChild(progressBar);

    container.appendChild(notif);

    setTimeout(() => {
        notif.remove();
    }, duration);
}