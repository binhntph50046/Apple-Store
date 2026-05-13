@extends('client.layouts.app')

@section('title', 'Live Chat')

@section('content')
    <meta name="id" content="{{ $id }}">
    <div class="container">
        @include('Chatify::layouts.headLinks')
        <div class="messenger">
            <div class="messenger-listView {{ !!$id ? 'conversation-active' : '' }}">
                {{-- Header and search bar --}}
                <div class="m-header">
                    <nav>
                        <a href=""><i class="fas fa-inbox"></i> <span class="messenger-headTitle">Tin nhắn</span> </a>

                    </nav>


                </div>
                {{-- tabs and lists --}}
                <div class="m-body contacts-container">
                    {{-- Lists [Users/Group] --}}
                    {{-- ---------------- [ User Tab ] ---------------- --}}
                    <div class="show messenger-tab users-tab app-scroll" data-view="users">
                        {{-- Favorites --}}
                        <div class="favorites-section">
                            <!-- <p class="messenger-title"><span>Favorites</span></p> -->
                            <div class="messenger-favorites app-scroll-hidden"></div>
                        </div>

                        {{-- Contact --}}
                        <div class="messenger-title-line">
                            <hr><span>Tất cả tin nhắn</span>
                            <hr>
                        </div>
                        <div class="listOfContacts" style="width: 100%;height: calc(100% - 272px);position: relative;">
                        </div>
                    </div>
                    {{-- ---------------- [ Search Tab ] ---------------- --}}
                    <div class="messenger-tab search-tab app-scroll" data-view="search">
                        {{-- items --}}
                        <p class="messenger-title"><span>Tìm kiếm...</span></p>
                        <div class="search-records">
                            <p class="message-hint center-el"><span>Tìm kiếm tin nhắn...</span></p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ------------------Messaging side---------------------- --}}
            <div class="messenger-messagingView">
                {{-- header title [conversation name] amd buttons --}}
                <div class="m-header m-header-messaging">
                    <nav class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                        {{-- header back button, avatar and user name --}}
                        <div class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                            <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                            <div class="avatar av-s header-avatar"
                                style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                            </div>
                            <a href="#" class="user-name">{{ $user->name ?? 'Admin' }}</a>
                        </div>
                        {{-- header buttons --}}
                        <nav class="m-header-right">
                            <a href="/"><i class="fas fa-home"></i></a>
                            <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
                        </nav>
                    </nav>
                    {{-- Internet connection --}}
                    <div class="internet-connection">
                        <span class="ic-connected">Connected</span>
                        <span class="ic-connecting">Connecting...</span>
                        <span class="ic-noInternet">No internet access</span>
                    </div>
                </div>

                {{-- Messaging area --}}
                <div class="m-body messages-container app-scroll">
                    <div class="messages">
                        <p class="message-hint center-el"><span>Click để chat</span></p>
                    </div>
                    {{-- Typing indicator --}}
                    <div class="typing-indicator">
                        <div class="message-card typing">
                            <div class="message">
                                <span class="typing-dots">
                                    <span class="dot dot-1"></span>
                                    <span class="dot dot-2"></span>
                                    <span class="dot dot-3"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
                @include('Chatify::layouts.sendForm')
            </div>
        </div>

        @include('Chatify::layouts.modals')
        @include('Chatify::layouts.footerLinks')
        <style>
            :root {
                --listView-header-height: 40px;
                --messenger-header-height: 120px;
                /* Match the padding-top */
                --background-color: #ffffff;
                /* Nền trắng để rõ nội dung */
                --message-bg: #f1f3f5;
                /* Nền tin nhắn nhạt */
                --message-sender-bg: #007bff;
                /* Màu tin nhắn người gửi */
                --message-sender-text: #ffffff;
                /* Chữ trắng cho tin nhắn người gửi */
                --border-color: #e9ecef;
                /* Viền nhạt */
                --spacing: 10px;
            }

            .messenger {
                height: 600px;
                max-height: 100%;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                display: flex;
                /* Ensure flex layout */
            }

            .messenger-messagingView {
                padding-top: 120px;
                background: var(--background-color);
            }

            .messenger-listView {
                width: 300px;
                border-right: 1px solid #dee2e6;
                background-color: #f8f9fa;
                padding-top: var(--messenger-header-height);
                overflow: hidden;
                /* Prevent overflow issues */
            }

            .messenger-messagingView {
                flex: 1;
                background-color: #ffffff;
                padding-top: var(--messenger-header-height);
                overflow: hidden;
                /* Prevent overflow issues */
            }

            .messenger-tab.users-tab.app-scroll,
            .messages-container.app-scroll {
                height: calc(100% - var(--messenger-header-height));
                /* Use available height */
                overflow-y: auto;
                /* Enable vertical scrolling */
                max-height: 500px;
                /* Adjust based on 600px - 120px */
            }

            .messenger-infoView {
                width: 280px;
                border-left: 1px solid #dee2e6;
                /* Viền trái */
                background-color: #f1f3f5;
            }

            .messenger-list-item {
                padding: 10px 15px;
                border-bottom: 1px solid #e9ecef;
                cursor: pointer;
                transition: background-color 0.2s ease;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .messenger-list-item:hover {
                background-color: #f0f4f8;
                /* Màu nền khi hover */
                border-left: 4px solid #1890ff;
                /* Thanh màu bên trái khi hover */
            }

            /* Nếu muốn thêm hiệu ứng chữ nổi bật khi hover */
            .messenger-list-item:hover .chatify-name {
                font-weight: 600;
                color: #1890ff;
            }

            .messenger-list-item.active {
                background-color: #e6f0ff;
                /* màu nền khi đã click */
                border-left: 4px solid #2180f3;
                /* viền trái nổi bật */
                font-weight: 600;
            }

            .messenger-infoView {
                display: none !important;
            }

            .messenger-infoView.active-tab {
                display: block !important;
            }

            .messenger-title-line {
                display: flex;
                align-items: center;
                text-align: center;
                margin: 10px 15px;
                color: #555;
                font-size: 13px;
            }

            .messenger-title-line hr {
                flex: 1;
                border: none;
                border-top: 1px solid #ccc;
                margin: 0 10px;
            }

            .messenger-title-line span {
                white-space: nowrap;
                font-weight: 600;
                font-size: 13px;
            }

            a {
                text-decoration: none;
                color: #0d6efd;
            }

            .messenger-listView a:hover {
                text-decoration: none;
            }

            .message-hint {
                margin: 20px;
                text-align: center;
            }

            .messenger-list-item td p span {
                float: right;
                padding-left: 10px;
            }

            .avatar {
                border: 1px solid #d9d9d9;
            }

            .messenger-list-item td p {
                margin-bottom: 4px;
                font-size: 14px;
                font-weight: bold;
            }

            .messenger-sendCard label {
                margin-top: 25px;
            }

            #message-form>button {
                height: 40px;
                margin-top: 16px;
            }

            .m-send {
                font-size: 14px;
                width: 100%;
                border: none;
                padding: 10px;
                outline: none;
                resize: none;
                background: transparent;
                font-family: sans-serif;
                height: 44px;
                max-height: 45px;
                margin-top: 10px;
            }

            ..m-list-active .activeStatus {
                border-color: var(--primary-color) !important;
                margin-bottom: 7px;
            }

            .messenger-sendCard svg:active {
                transform: scale(0.9);
                color: #0d6efdb8;
            }

            .m-list-active,
            .m-list-active:hover,
            .m-list-active:focus Specificity: (0, 2, 0) {
                background: #3da2ff !important;
            }
        </style>
        <script>
            const userId = {{ auth()->id() ?? 'null' }};
            const adminId = {{ $user->id }};

            function loadLivechat() {
                fetch('/livechat/messages')
                    .then(res => res.json())
                    .then(data => {
                        const box = document.getElementById('livechatMessages');
                        box.innerHTML = ''; // Xóa cũ
                        let alreadySent = false;

                        data.forEach(msg => {
                            appendLivechatMessage(msg.from_user_id == userId ? 'user' : 'bot', msg.message);
                            if (msg.from_user_id == userId) {
                                alreadySent = true;
                            }
                        });

                        // Nếu chưa từng gửi => tự gửi "Xin chào"
                        if (!alreadySent) {
                            const defaultMsg = 'Xin chào! Tôi cần hỗ trợ.';
                            appendLivechatMessage('user', defaultMsg);

                            fetch('/livechat/send', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    },
                                    body: JSON.stringify({
                                        message: defaultMsg,
                                        to_user_id: adminId
                                    })
                                })
                                .then(res => res.json())
                                .then(data => console.log('Đã tự động gửi chào:', data))
                                .catch(err => console.error('Tự động gửi lỗi:', err));
                        }

                        box.scrollTop = box.scrollHeight;
                    })
                    .catch(err => console.error('Lỗi tải tin nhắn:', err));
            }
        </script>
    </div>


@endsection
