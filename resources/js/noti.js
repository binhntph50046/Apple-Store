import Pusher from 'pusher-js';

    // Enable Pusher logging - chỉ bật khi debug
    Pusher.logToConsole = true;

    // Khởi tạo Pusher
    var pusher = new Pusher('1ebdf458c75dbaeac749', {
        cluster: 'ap1'
    });

    // Sub kênh chung cho admin
    var channelNotif = pusher.subscribe('admin-notifications');

    // Hàm thêm thông báo vào danh sách
    function appendNotification(data) {
        var notifList = document.getElementById('notif-list');
        if (notifList) {
            var a = document.createElement('a');
            a.className = 'list-group-item list-group-item-action notif-item'; // ⚠️ Thêm class này
            a.href = data.url || '#';
            a.dataset.id = data.id || ''; // Để Ajax xử lý đánh dấu đã đọc nếu có ID
            a.innerHTML = `
            <div class="d-flex justify-content-between">
                <div>
                    <b>${data.title || 'Thông báo'}</b><br>
                    <small>${data.message || ''}</small>
                </div>
                <div><span class="badge bg-success">Mới</span></div>
            </div>
        `;
            notifList.prepend(a);
            // 🛠️ Gắn lại sự kiện click
            a.addEventListener('click', function () {
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
    }

    // Lắng nghe sự kiện tài khoản mới
    channelNotif.bind('user.created', function (data) {
        toastr.success('Email: ' + data.email, 'Tài khoản mới: ' + data.name);
        appendNotification({
            title: 'Tài khoản mới',
            message: data.name + ' (' + data.email + ') vừa đăng ký.',
            url: data.url || '#'
        });
    });

    // Lắng nghe sự kiện đơn hàng mới
    channelNotif.bind('order.created', function (data) {
        toastr.info(
            'Khách hàng: ' + data.user + '<br>Đơn hàng #' + data.order_id ,
            'Đơn hàng mới'
        );
        appendNotification({
            title: 'Đơn hàng mới',
            message: 'Khách hàng: ' + data.user + ', Đơn hàng #' + data.order_id,
            url: data.url || '#'
        });
    });
    // Lắng nghe sự kiện chat mới
    channelNotif.bind('message.created', function (data) {
        toastr.info(
            'Tin nhắn mới từ:' + data.from_user_id + ': ' + data.message,
            'Tin nhắn mới'
        );
        appendNotification({
            title: 'Tin nhắn mới',
            message: 'Người dùng" ' + data.from_user_id + ' vừa gửi: "' + data.message + '"',
            url: data.url || '#'
        });
    });

    // Lắng nghe sự kiện yêu cầu hoàn hàng mới
    channelNotif.bind('return.created', function (data) {
        toastr.warning(
            'Khách hàng: ' + data.user + '<br>Đơn hàng: ' + data.order_code + '<br>Lý do: ' + data.reason,
            'Yêu cầu hoàn hàng mới'
        );
        appendNotification({
            title: 'Yêu cầu hoàn hàng',
            message: 'Khách hàng ' + data.user + ' vừa gửi yêu cầu hoàn hàng cho đơn ' + data.order_code + '.',
            // url: '/admin/order-returns/' + data.return_id // bạn cần tạo đúng route này nhé
            url: data.url || '#'
        });
    });
    // Lắng nghe sự kiện liên hệ mới
    channelNotif.bind('contact.submitted', function (data) {
        toastr.info(
            'Email: ' + data.email + '<br>Nội dung: ' + data.message,
            'Yêu cầu hỗ trợ từ ' + data.name
        );
        appendNotification({
            title: 'Yêu cầu hỗ trợ mới',
            message: data.name + ' vừa gửi một yêu cầu hỗ trợ.',
            url: data.url
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.notif-item').forEach(function (el) {
            el.addEventListener('click', function (e) {
                const id = this.dataset.id;
                fetch(`/notifications/read/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
    document.getElementById('mark-all-read')?.addEventListener('click', function () {
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
    const typeColorMap = {
        'user.created': 'text-success',
        'order.created': 'text-info',
        'return.created': 'text-warning',
        'contact.submitted': 'text-warning',
        'message.created': 'text-info',
       
    };

    // const colorClass = typeColorMap[data.type] || typeColorMap['default'];
    const timeNow = new Date();
    const humanTime = timeNow.toLocaleTimeString('vi-VN'); 

    a.innerHTML = `
        <div class="d-flex justify-content-between">
            <div>
                <b class="${typeColorMap}">${data.title || 'Thông báo'}</b><br>
                <small>${data.message || ''}</small>
                <small class="text-muted d-block mt-1">${humanTime}</small>
            </div>
            <div><span class="badge bg-success">Mới</span></div>
        </div>
    `;  