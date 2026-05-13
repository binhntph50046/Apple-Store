import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    encrypted: true,
    forceTLS: false,
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    enabledTransports: ["ws", "wss"],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});

// Lắng nghe sự kiện new-chat
window.Echo.private('admin.notifications')
    .listen('.new-chat', (e) => {
        console.log('🎉 New chat event received:', e);
        showToastNotification(e.name + ' vừa nhắn tin cho bạn');
        addNotificationToDropdown(e);
        increaseBellCount();
    });

function showToastNotification(message) {
    let toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.innerHTML = `
        <div class="toast align-items-center text-white bg-primary border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

function addNotificationToDropdown(data) {
    const dropdown = document.querySelector('.dropdown-notification .list-group');
    const link = `/admin/livechat/${data.user_id}`;
    const html = `
        <a href="${link}" class="list-group-item list-group-item-action">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <img src="${data.avatar}" alt="user-image" class="user-avtar">
                </div>
                <div class="flex-grow-1 ms-1">
                    <span class="float-end text-muted">${data.time}</span>
                    <p class="text-body mb-1"><b>${data.name}</b> vừa nhắn tin cho bạn</p>
                </div>
            </div>
        </a>`;
    dropdown.insertAdjacentHTML('afterbegin', html);
}

function increaseBellCount() {
    let badge = document.querySelector('#notif-badge');
    if (!badge) {
        const icon = document.querySelector('.ti-bell');
        icon.insertAdjacentHTML('afterend', `<span id="notif-badge" class="badge bg-danger">1</span>`);
    } else {
        badge.classList.remove('d-none');
        badge.innerText = parseInt(badge.innerText) + 1;
    }
}
