<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.currentUserId = {{ auth()->id() }};
    </script>
    <style>
        .chatbot-icon svg {
            width: 38px;
            height: 38px;
        }

        .chatbot-window {
            display: none;
            flex-direction: column;
            position: fixed;
            bottom: 110px;
            right: 30px;
            width: 350px;
            max-width: 95vw;
            height: 550px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.18);
            z-index: 2002;
            overflow: hidden;
            animation: fadeInUp 0.3s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chatbot-header {
            background: #222;
            color: #fff;
            padding: 16px;
            font-size: 1.2rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chatbot-header .close-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
        }

        .chatbot-messages {
            flex: 1;
            padding: 16px;
            background: #f7f7f7;
            overflow-y: auto;
            font-size: 1rem;
        }

        .chatbot-message {
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
        }

        .chatbot-message.user {
            align-items: flex-end;
        }

        .chatbot-message.bot {
            align-items: flex-start;
        }

        .chatbot-message.bot.loading .chatbot-bubble {
            color: #888;
            font-style: italic;
        }

        .chatbot-bubble {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 16px;
            margin-bottom: 2px;
            word-break: break-word;
        }

        .chatbot-message.user .chatbot-bubble {
            background: #007bff;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .chatbot-message.bot .chatbot-bubble {
            background: #e5e5e5;
            color: #222;
            border-bottom-left-radius: 4px;
        }

        .chatbot-input-area {
            display: flex;
            border-top: 1px solid #eee;
            background: #fff;
            padding: 10px;
        }

        .chatbot-input-area input {
            flex: 1;
            border: none;
            outline: none;
            padding: 10px;
            border-radius: 8px;
            font-size: 1rem;
            background: #f1f1f1;
        }

        .chatbot-input-area button {
            margin-left: 8px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0 18px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .chatbot-input-area button:hover {
            background: #0056b3;
        }

        @media (max-width: 500px) {
            .chatbot-window {
                width: 98vw;
                right: 1vw;
            }
        }

        /* Hiệu ứng rung lắc cho icon khi chưa mở */
        @keyframes shake {
            0% {
                transform: rotate(0deg);
            }

            20% {
                transform: rotate(-10deg);
            }

            40% {
                transform: rotate(10deg);
            }

            60% {
                transform: rotate(-10deg);
            }

            80% {
                transform: rotate(10deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        /* Ẩn icon chat khi mở khung chat, hiện dấu X */
        .chatbot-icon .icon-chat {
            display: block;
        }

        .chatbot-icon .icon-close {
            display: none;
        }

        .chatbot-icon.open .icon-chat {
            display: none;
        }

        .chatbot-icon.open .icon-close {
            display: block;
        }

        .chatbot-icon {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            background: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
            border: 4px solid #fff;
            z-index: 2001;
            transition: box-shadow 0.2s;
            overflow: visible;
        }

        .chatbot-icon.shake {
            animation: shake 1s infinite;
        }

        .chatbot-icon.shake::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 65px;
            height: 65px;
            border-radius: 50%;
            border: 4px solid #fff;
            opacity: 0.6;
            z-index: -1;
            animation: pulse-border 1.2s infinite;
        }

        @keyframes pulse-border {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.6;
            }

            70% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0.1;
            }

            100% {
                transform: translate(-50%, -50%) scale(1.8);
                opacity: 0;
            }
        }

        /* CSS cho icon live chat */
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

        /* Thêm kiểu dáng cho nhãn "Chat 24/7" */
        .chat-label {
            font-size: 10px;
            color: #fff;
            font-weight: bold;
            margin-top: 2px;
            text-align: center;
            line-height: 1;
            text-transform: uppercase;
        }

        /* Hiệu ứng rung cho live-chat-icon khi nhấn */
        .live-chat-icon.shake {
            animation: shake 0.5s;
            /* Thực hiện rung 1 lần khi nhấn */
        }
    </style>
</head>

<body>

    <div class="chatbot-icon shake" id="chatbotIcon" onclick="toggleChatbot()">
        <!-- Icon chat -->
        <span class="icon-chat">
            <svg width="40" height="40" viewBox="0 0 38 38" fill="none">
                <!-- Thân dưới -->
                <ellipse cx="19" cy="33" rx="9" ry="3" fill="#E6E6F0" />
                <!-- Thân chính -->
                <rect x="6" y="10" width="26" height="16" rx="8" fill="#fff" stroke="#E6E6F0"
                    stroke-width="2" />
                <!-- Mặt đen -->
                <rect x="10" y="13" width="18" height="10" rx="5" fill="#181A2A" />
                <!-- Mắt trái -->
                <circle cx="15" cy="18" r="2" fill="#00E1FF" />
                <!-- Mắt phải -->
                <circle cx="23" cy="18" r="2" fill="#00E1FF" />
                <!-- Miệng cười -->
                <path d="M16.5 21c1 1 4 1 5 0" stroke="#fff" stroke-width="1.2" stroke-linecap="round" />
                <!-- Tai trái -->
                <ellipse cx="6" cy="18" rx="2" ry="2.5" fill="#fff" stroke="#E6E6F0"
                    stroke-width="1" />
                <!-- Tai phải -->
                <ellipse cx="32" cy="18" rx="2" ry="2.5" fill="#fff" stroke="#E6E6F0"
                    stroke-width="1" />
                <!-- 2 Ăng-ten -->
                <rect x="17.2" y="5" width="1.6" height="6" rx="0.8" fill="#fff" />
                <rect x="19.2" y="5" width="1.6" height="6" rx="0.8" fill="#fff" />
                <!-- Đầu ăng-ten -->
                <circle cx="18" cy="5" r="1.2" fill="#fff" stroke="#E6E6F0" stroke-width="0.7" />
                <circle cx="20" cy="5" r="1.2" fill="#fff" stroke="#E6E6F0" stroke-width="0.7" />
            </svg>
        </span>
        <!-- Icon close (dấu X) -->
        <span class="icon-close">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                <line x1="6" y1="6" x2="22" y2="22" stroke="white" stroke-width="3"
                    stroke-linecap="round" />
                <line x1="22" y1="6" x2="6" y2="22" stroke="white" stroke-width="3"
                    stroke-linecap="round" />
            </svg>
        </span>
    </div>
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

    <!-- Khung chat -->
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <span>🤖 Chatbot Hỗ trợ</span>
            <button class="close-btn" onclick="toggleChatbot()">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot">
                <div class="chatbot-bubble">Xin chào! Tôi có thể giúp gì cho bạn?</div>
            </div>
        </div>
        <form class="chatbot-input-area" id="chatbotForm" autocomplete="off" onsubmit="return sendMessagee(event)">
            <input type="text" id="chatbotInput" placeholder="Nhập câu hỏi..." autocomplete="off" required>
            <button type="submit">Gửi</button>
        </form>
    </div>

    <script>
        function toggleChatbot() {
            const win = document.getElementById('chatbotWindow');
            const icon = document.getElementById('chatbotIcon');
            if (win.style.display === 'flex') {
                win.style.display = 'none';
                icon.classList.remove('open');
                icon.classList.add('shake');
            } else {
                win.style.display = 'flex';
                icon.classList.add('open');
                icon.classList.remove('shake');
            }
        }

        function sendMessagee(e) {
            e.preventDefault();
            const input = document.getElementById('chatbotInput');
            const msg = input.value.trim();
            if (!msg) return false;

            appendMessage('user', msg);
            input.value = '';
            input.focus();

            const messages = document.getElementById('chatbotMessages');
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chatbot-message bot loading';
            loadingDiv.innerHTML = `<div class="chatbot-bubble"><span id="typing-dots">.</span></div>`;
            messages.appendChild(loadingDiv);
            messages.scrollTop = messages.scrollHeight;

            let dotCount = 1;
            const typingDots = loadingDiv.querySelector('#typing-dots');
            const dotsInterval = setInterval(() => {
                dotCount = (dotCount % 3) + 1;
                typingDots.textContent = '.'.repeat(dotCount);
            }, 400);

            fetch('{{ route('chatbot.ask') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        text: msg
                    })
                })
                .then(res => res.json())
                .then(data => {
                    clearInterval(dotsInterval);
                    loadingDiv.remove();
                    appendMessage('bot', data.answer);
                })
                .catch(() => {
                    clearInterval(dotsInterval);
                    loadingDiv.remove();
                    appendMessage('bot', 'Có lỗi xảy ra, vui lòng thử lại sau.');
                });

            return false;
        }

        // function appendMessage(sender, text) {
        //     const messages = document.getElementById('chatbotMessages');
        //     const msgDiv = document.createElement('div');
        //     msgDiv.className = 'chatbot-message ' + sender;
        //     // Kiểm tra và thay thế link bằng nút nếu cần
        //     if (text.includes('<a href="/chat"')) {
        //         msgDiv.innerHTML = `<div class="chatbot-bubble">${text.replace('<a href="/chat" target="_blank">', '<button onclick="window.open(\'/chat\', \'_blank\')">').replace('</a>', '</button>')}</div>`;
        //     } else {
        //         msgDiv.innerHTML = `<div class="chatbot-bubble">${escapeHtml(text)}</div>`;
        //     }
        //     messages.appendChild(msgDiv);
        //     messages.scrollTop = messages.scrollHeight;
        // }

        // function escapeHtml(text) {
        //     return text.replace(/[&<>"']/g, function (m) {
        //         return ({
        //             '&': '&amp;',
        //             '<': '&lt;',
        //             '>': '&gt;',
        //             '"': '&quot;',
        //             "'": '&#039;'
        //         })[m];
        //     });
        // }

        // ========== Thêm hàm sanitizeHTML (an toàn hơn innerHTML thô) ==========
        function sanitizeHTML(html) {
            // Chỉ cho phép các thẻ và attribute này
            const allowedTags = {
                'A': ['href', 'target', 'rel'],
                'UL': [],
                'OL': [],
                'LI': [],
                'BR': [],
                'STRONG': [],
                'B': [],
                'EM': [],
                'I': [],
                'P': [],
                'SPAN': []
            };

            const doc = new DOMParser().parseFromString(html, 'text/html');

            function clean(node) {
                // Duyệt children trước khi thao tác để tránh bỏ sót
                const children = Array.from(node.childNodes);
                for (const child of children) clean(child);

                if (childIsElement(node)) {
                    const name = node.nodeName.toUpperCase();
                    if (!allowedTags[name]) {
                        // Tháo thẻ nhưng giữ children (unwrap)
                        const parent = node.parentNode;
                        while (node.firstChild) parent.insertBefore(node.firstChild, node);
                        parent.removeChild(node);
                        return;
                    }
                    // Lọc attributes chỉ giữ những thuộc tính được phép
                    const allowedAttrs = allowedTags[name];
                    Array.from(node.attributes).forEach(attr => {
                        if (!allowedAttrs.includes(attr.name)) node.removeAttribute(attr.name);
                    });

                    // Với thẻ <a>, kiểm tra href và ép target+rel
                    if (name === 'A') {
                        const href = node.getAttribute('href') || '';
                        // chỉ cho phép /... , http(s):// và mailto:
                        if (!/^\/|^https?:\/\/|^mailto:/i.test(href)) {
                            node.removeAttribute('href');
                        } else {
                            node.setAttribute('target', '_blank');
                            node.setAttribute('rel', 'noopener noreferrer');
                        }
                    }
                }
            }

            function childIsElement(n) {
                return n && n.nodeType === Node.ELEMENT_NODE;
            }

            // Dọn dẹp body
            Array.from(doc.body.childNodes).forEach(clean);
            return doc.body.innerHTML;
        }

        // ========== Thay thế appendMessage + escapeHtml ==========

        function appendMessage(sender, text) {
            const messages = document.getElementById('chatbotMessages');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'chatbot-message ' + sender;

            const bubble = document.createElement('div');
            bubble.className = 'chatbot-bubble';

            if (sender === 'bot') {
                // Nếu có dấu <...> khả nghi -> coi là HTML do server tạo ra
                if (/<[a-z][\s\S]*>/i.test(text)) {
                    // sanitize trước khi chèn
                    const safe = sanitizeHTML(text);
                    bubble.innerHTML = safe;
                } else {
                    // plain text
                    bubble.textContent = text;
                }
            } else {
                // user: luôn escape -> dùng textContent
                bubble.textContent = text;
            }

            msgDiv.appendChild(bubble);
            messages.appendChild(msgDiv);
            messages.scrollTop = messages.scrollHeight;
        }

        // Bạn có thể xóa hàm escapeHtml cũ hoặc để lại cho tham khảo — không còn dùng nữa.


        document.getElementById('chatbotInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') sendMessagee(e);
        });

        // Chuyển hướng đến /chat khi nhấn icon live chat với hiệu ứng rung
        function redirectToChat() {
            const liveChatIcon = document.getElementById('liveChatIcon');
            liveChatIcon.classList.add('shake'); // Thêm hiệu ứng rung
            setTimeout(() => {
                window.location.href = '/chat'; // Chuyển hướng sau khi rung xong
            }, 500); // Độ trễ 500ms để hiệu ứng hoàn thành
        }
    </script>
    <!-- Đảm bảo đã load Pusher trước
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>
    // 0. Kiểm tra script có chạy không
    console.log('[Debug] Chat badge script start');

    // 1. Đọc CSRF & user-id
    const userIdMeta = document.head.querySelector('meta[name="user-id"]');
    const csrfMeta = document.head.querySelector('meta[name="csrf-token"]');
    console.log('[Debug] user-id meta:', userIdMeta, 'csrf meta:', csrfMeta);
    const userId = userIdMeta ? userIdMeta.content : null;
    const badge = document.getElementById('unreadBadge');
    console.log('[Debug] userId:', userId, 'badge element:', badge);

    if (!userId || !badge) {
        console.warn('[Debug] Missing userId or badge element, aborting.');
    } else {
        // 2. Hàm cập nhật badge
        function updateBadge(count) {
            console.log('[Debug] updateBadge(', count, ')');
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        // 3. Lấy count ban đầu
        console.log('[Debug] Fetching initial unread count');
        fetch('/chat/unread-count', {
                headers: {
                    'X-CSRF-TOKEN': csrfMeta.content
                }
            })
            .then(res => {
                console.log('[Debug] /chat/unread-count response status:', res.status);
                return res.json();
            })
            .then(data => {
                console.log('[Debug] initial unread data:', data);
                if (data.unread != null) updateBadge(data.unread);
            })
            .catch(err => console.error('[Debug] fetch unread-count error:', err));

        // 4. Cấu hình Pusher + Echo
        console.log('[Debug] Initializing Echo');
        window.Pusher.logToConsole = true; // bật debug Pusher
        window.Echo = new window.Echo.constructor({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true,
            encrypted: true,
        });

        // 5. Lắng nghe realtime
        const channelName = `chat.${userId}`;
        console.log('[Debug] Subscribing to channel:', channelName);
        window.Echo.private(channelName)
            .listen('MessageSent', e => {
                console.log('[Debug] Received MessageSent event:', e);
                if (e.message.to_id == userId) {
                    const cur = parseInt(badge.textContent || '0', 10) + 1;
                    console.log('[Debug] Increment badge to:', cur);
                    updateBadge(cur);
                }
            })
            .error(err => console.error('[Debug] Echo subscription error:', err));

        // 6. Khi click icon, mark seen và reset badge
        document.getElementById('liveChatIcon').addEventListener('click', () => {
            console.log('[Debug] liveChatIcon clicked, marking seen');
            fetch('/chat/mark-seen', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfMeta.content
                    }
                })
                .then(res => {
                    console.log('[Debug] mark-seen response status:', res.status);
                    updateBadge(0);
                })
                .catch(err => console.error('[Debug] mark-seen error:', err));
        });
    }
</script> -->

</body>

</html>
