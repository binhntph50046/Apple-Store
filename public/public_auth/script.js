function hideAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 3000);
    }
}

hideAlert('success-alert');
hideAlert('error-alert');
