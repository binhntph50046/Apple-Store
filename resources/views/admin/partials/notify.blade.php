<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        <style>
.live-chat-icon {
    position: fixed;
    bottom: 110px;
    right: 30px;
    width: 70px;
    height: 70px;
    background: rgb(54 94 173);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
    border: 4px solid #fff;
    z-index: 2001;
    transition: box-shadow 0.2s;
}

.live-chat-icon svg {
    width: 24px;
    height: 24px;
    fill: #fff;
}

.live-chat-icon:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.chat-label {
    font-size: 10px;
    color: #fff;
    font-weight: bold;
    margin-top: 2px;
    text-align: center;
    line-height: 1;
    text-transform: uppercase;
}

.live-chat-icon.shake {
    animation: shake 0.5s;
}

/* Rung */
@keyframes shake {
    0% { transform: rotate(0deg); }
    20% { transform: rotate(-10deg); }
    40% { transform: rotate(10deg); }
    60% { transform: rotate(-10deg); }
    80% { transform: rotate(10deg); }
    100% { transform: rotate(0deg); }
}
</style>

    </style>
</head>
<body>
    <!-- Icon live chat -->
<div class="live-chat-icon" id="liveChatIcon" onclick="redirectToChat()">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
            d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM6 9H18V11H6V9ZM14 14H6V12H14V14ZM18 7H6V5H18V7Z"
            fill="white" />
    </svg>
    <span class="chat-label">Chat 24/7</span>

    <!-- 🔴 Badge số lượng chưa đọc -->
    <span id="unreadBadge"
        style="position:absolute; top:-4px; right:-4px; background:red; color:white;
        font-size:12px; font-weight:bold; border-radius:50%; padding:3px 6px;
        display:none;">0</span>
</div>
<script>
    function redirectToChat() {
        const liveChatIcon = document.getElementById('liveChatIcon');
        liveChatIcon.classList.add('shake');
        setTimeout(() => {
            window.location.href = '/admin/livechat';
        }, 500);
    }
</script>

</body>

</html> -->
@push('scripts')
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <script>
        // Enable Pusher logging - chỉ bật khi debug
        Pusher.logToConsole = true;

        // Lấy CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;

        // Khởi tạo 1 instance Pusher cho cả public + private channels
        const pusher = new Pusher('1ebdf458c75dbaeac749', {
            cluster: 'ap1',
            authEndpoint: '/chat/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': token
                }
            }
        });


        // Sub kênh chung cho admin
        var channelNotif = pusher.subscribe('admin-notifications');

        function timeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            if (seconds === 0) return 'Now';
            if (seconds < 60) return `${seconds} seconds ago`;
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes} minutes ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours} hours ago`;
            const days = Math.floor(hours / 24);
            return `${days} days ago`;
        }

        function updateAllTimes() {
            document.querySelectorAll('.notif-item').forEach(el => {
                const ts = Number(el.dataset.createdAt);
                if (!ts) return;
                const human = timeAgo(new Date(ts));
                const node = el.querySelector('.text-muted');
                if (node) node.textContent = human;
            });
        }


        // Hàm thêm thông báo vào danh sách
        function appendNotification(data) {
            var notifList = document.getElementById('notif-list');
            if (notifList) {
                var a = document.createElement('a');
                a.className = 'list-group-item list-group-item-action notif-item'; // ⚠️ Thêm class này
                a.href = data.url || '#';
                a.dataset.id = data.id || ''; // Để Ajax xử lý đánh dấu đã đọc nếu có ID

                const type = data.type || 'default';
                const typeColor = {
                    'user_created': 'text-success',
                    'order_created': 'text-info',
                    'return_created': 'text-warning',
                    'contact_submitted': 'text-warning',
                    'message_created': 'text-info',
                    'error': 'text-danger',
                    'default': 'text-muted'
                } [type];

                // ✅ Thời gian hiện tại (sửa đúng vị trí)
                const createdAt = data.created_at ?
                    new Date(data.created_at) :
                    new Date();
                a.dataset.createdAt = createdAt.getTime();

                const humanTime = timeAgo(createdAt);

                // ✅ Render nội dung
                a.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <div>
                                <b class="${typeColor}">${data.title || 'Thông báo'}</b><br>
                                <small>${data.message || ''}</small>
                                <small class="text-muted d-block mt-1">${humanTime}</small>
                            </div>
                            <div><span class="badge bg-success">Mới</span></div>
                        </div>
                    `;

                notifList.prepend(a);
                // 🛠️ Gắn lại sự kiện click
                a.addEventListener('click', function() {
                    const id = this.dataset.id;
                    if (!id) return;

                    fetch(`/notifications/read/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    }).then(res => {
                        if (res.ok) {
                            const badge = document.getElementById('notif-badge');
                            let count = parseInt(badge.textContent || '0');
                            count = Math.max(count - 1, 0);
                            badge.textContent = count;
                            if (count === 0) badge.classList.add('d-none');
                        }
                    });
                });

                // 🟢 Cập nhật badge đếm
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    badge.classList.remove('d-none');
                    badge.textContent = parseInt(badge.textContent || '0') + 1;
                }
            }
            updateAllTimes();
        }

        // Lắng nghe sự kiện tài khoản mới
        channelNotif.bind('user.created', function(data) {
            toastr.success('Email: ' + data.email, 'Tài khoản mới: ' + data.name);
            toastr.options.onclick = function() {
                window.location.href = data.url;
            };
            appendNotification({
                type: 'user_created',
                title: 'Tài khoản mới',
                message: data.name + ' (' + data.email + ') vừa đăng ký.',
                url: data.url
            });
        });

        // Lắng nghe sự kiện đơn hàng mới
        channelNotif.bind('order.created', function(data) {
            toastr.info(
                'Khách hàng: ' + data.user + '<br>Đơn hàng #' + data.order_id,
                'Đơn hàng mới'
            );
            toastr.options.onclick = function() {
                window.location.href = data.url;
            };
            appendNotification({
                type: 'order_created',
                title: 'Đơn hàng mới',
                message: 'Khách hàng: ' + data.user + ', vừa đặt đơn hàng #' + data.order_id,
                url: data.url
            });
        });
        // Lắng nghe sự kiện chat mới
        channelNotif.bind('message.created', function(data) {
            toastr.info(
                data.user_name + ' vừa gửi: "' + data.message + '"',
                'Tin nhắn mới'
            );
            toastr.options.onclick = function() {
                window.location.href = data.url;
            };
            appendNotification({
                type: 'message_created',
                title: 'Tin nhắn mới',
                message: data.user_name + ' vừa gửi: "' + data.message + '"',
                url: data.url
            });
        });

        // Lắng nghe sự kiện yêu cầu hoàn hàng mới
        channelNotif.bind('return.created', function(data) {
            toastr.warning(
                'Khách hàng: ' + data.user + '<br>Đơn hàng: ' + data.order_code + '<br>Lý do: ' + data.reason,
                'Yêu cầu hoàn hàng mới'
            );
            toastr.options.onclick = function() {
                window.location.href = data.url;
            };
            appendNotification({
                type: 'return_created',
                title: 'Yêu cầu hoàn hàng',
                message: 'Khách hàng ' + data.user + ' vừa gửi yêu cầu hoàn hàng cho đơn ' + data
                    .order_code + '.',
                url: data.url
            });
        });
        // Lắng nghe sự kiện liên hệ mới
        channelNotif.bind('contact.submitted', function(data) {
            toastr.info(
                'Email: ' + data.email + '<br>Nội dung: ' + data.message,
                'Yêu cầu hỗ trợ từ ' + data.name
            );
            toastr.options.onclick = function() {
                window.location.href = data.url;
            };
            appendNotification({
                type: 'contact_submitted',
                title: 'Yêu cầu hỗ trợ mới',
                message: data.name + ' vừa gửi một yêu cầu hỗ trợ.',
                url: data.url
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.notif-item').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    const id = this.dataset.id;
                    fetch(`/notifications/read/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    }).then(res => {
                        if (res.ok) {
                            // Ẩn chữ "Mới"
                            this.querySelector('.badge.bg-success')?.remove();

                            // Giảm số badge
                            const badge = document.getElementById('notif-badge');
                            let current = parseInt(badge.textContent || '0');
                            current = Math.max(current - 1, 0);
                            badge.textContent = current;
                            if (current === 0) {
                                badge.classList.add('d-none');
                            }
                        }
                    });
                });
            });
        });

        // Xử lý sự kiện click cho nút "Đánh dấu tất cả đã đọc"
        document.getElementById('mark-all-read')?.addEventListener('click', function() {
            fetch(`/notifications/read-all`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(res => {
                if (res.ok) {
                    // Ẩn tất cả tag "Mới"
                    document.querySelectorAll('.badge.bg-success').forEach(el => el.remove());

                    // Cập nhật badge về 0
                    const badge = document.getElementById('notif-badge');
                    badge.textContent = '0';
                    badge.classList.add('d-none');
                }
            });
        });

        const meId = {{ auth()->id() }};
        const chatChannel = pusher.subscribe('private-chatify.' + meId);
        chatChannel.bind('messaging', data => {
            const body = document.querySelector('.messages');
            body.insertAdjacentHTML('beforeend', data.message);
            body.parentElement.scrollTop = body.parentElement.scrollHeight;
        });

        // 3. Fetch lịch sử chat khi DOM load xong
        document.addEventListener('DOMContentLoaded', () => {
            const otherId = {{ $id ?? 'null' }};
            if (!otherId) return;

            fetch('/fetchMessages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: otherId
                    })
                })
                .then(r => r.json())
                .then(json => {
                    const body = document.querySelector('.messages');
                    body.innerHTML = json.html;
                    body.parentElement.scrollTop = body.parentElement.scrollHeight;
                })
                .catch(console.error);
        });
    </script>
@endpush
