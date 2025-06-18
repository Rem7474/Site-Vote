// Toast notification system
function showToast(message, type = 'success') {
    let toast = document.getElementById('toast-notif');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-notif';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.className = 'toast show ' + (type === 'error' ? 'error' : 'success');
    setTimeout(() => {
        toast.className = 'toast ' + (type === 'error' ? 'error' : 'success');
    }, 3500);
}
// Pour affichage automatique via PHP :
window.addEventListener('DOMContentLoaded', function() {
    if (window.toastMessage) {
        showToast(window.toastMessage, window.toastType || 'success');
    }
});
