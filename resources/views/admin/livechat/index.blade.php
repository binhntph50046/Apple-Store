@extends('admin.layouts.app')

@section('title', 'Tin nhắn')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Tin nhắn</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item" aria-current="page">Tin nhắn</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @include('Chatify::layouts.headLinks')
                            <div class="messenger">
                                {{-- ----------------------Users/Groups lists side---------------------- --}}
                                <div class="messenger-listView {{ !!$id ? 'conversation-active' : '' }}">
                                    {{-- Header and search bar --}}
                                    <div class="m-header">
                                        <nav>
                                            <a href="#"><i class="fas fa-inbox"></i> <span
                                                    class="messenger-headTitle">TIN NHẮN</span> </a>
                                            {{-- header buttons --}}
                                            <nav class="m-header-right">
                                                            <!-- <a href="#"><i class="fas fa-cog settings-btn"></i></a> -->
                                                            <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                                                        </nav>
                                        </nav>
                                        {{-- Search input --}}
                                        <input type="text" class="messenger-search" placeholder="Tìm kiếm"
                                            style="border: 1px solid #D3D3D3;" />

                                    </div>
                                    {{-- tabs and lists --}}
                                    <div class="m-body contacts-container">
                                        {{-- Lists [Users/Group] --}}
                                        {{-- ---------------- [ User Tab ] ---------------- --}}
                                        <div class="show messenger-tab users-tab app-scroll" data-view="users">
                                            {{-- Favorites --}}
                                            

                                            {{-- Saved Messages --}}
                                            <div class="favorites-section">
                                                <div class="messenger-title">
                                                    <hr><span>Favorites</span><hr>
                                                </div>
                                                <div class="messenger-favorites app-scroll-hidden"></div>
                                            </div>

                                            {{-- Contact --}}
                                            <div class="messenger-title-line">
                                                <hr><span>Tất cả tin nhắn</span>
                                                <hr>
                                            </div>
                                            <div class="listOfContacts"
                                                style="width: 100%;height: calc(100% - 272px);position: relative;"></div>
                                        </div>
                                        {{-- ---------------- [ Search Tab ] ---------------- --}}
                                        <div class="messenger-tab search-tab app-scroll" data-view="search">
                                            {{-- items --}}
                                            <p class="messenger-title"><span>Search</span></p>
                                            <div class="search-records">
                                                <p class="message-hint center-el"><span>Type to search..</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ----------------------Messaging side---------------------- --}}
                                <div class="messenger-messagingView">
                                    {{-- header title [conversation name] amd buttons --}}
                                    <div class="m-header m-header-messaging">
                                        <nav
                                            class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                                            {{-- header back button, avatar and user name --}}
                                            <div
                                                class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                                                <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                                                <div class="avatar av-s header-avatar"
                                                    style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                                                </div>
                                                <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                                            </div>
                                            {{-- header buttons --}}
                                            <nav class="m-header-right">
                                                <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a>
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
                                            <p class="message-hint center-el"><span>Vui lòng click người dùng để nhắn
                                                    tin</span></p>
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
                                    {{-- Send Message Form --}}
                                    @include('Chatify::layouts.sendForm')
                                </div>
                                {{-- ---------------------- Info side ---------------------- --}}
                                <div class="messenger-infoView app-scroll">
                                    {{-- nav actions --}}
                                    <nav>
                                        <p>Thông tin cuộc trò chuyện</p>
                                        <a href="#" class="close-infoSide"><i class="fas fa-times"></i></a>
                                    </nav>
                                    {!! view('Chatify::layouts.info')->render() !!}
                                </div>
                            </div>

                            @include('Chatify::layouts.modals')
                            @include('Chatify::layouts.footerLinks')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        
        .messenger-list-item td p span {
  margin-left: 15px;
  float: right;
}
        .messenger-title > span {
  position: relative;
  padding: 0px 10px;
  z-index: 1;
  font-weight:bold;
}
        .messenger {
            height: 650px;
            max-height: 100%;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .messenger-listView {
            width: 300px;
            border-right: 1px solid #dee2e6;
            /* Viền phải */
            background-color: #f8f9fa;
        }

        .messenger-messagingView {
            flex: 1;
            background-color: #ffffff;
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
            border-top: 1px solid #000000;
            margin: 0 10px;
        }

        .messenger-title-line span {
            white-space: nowrap;
            font-weight: 600;
            font-size: 13px;
        }
        .messenger-title {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 10px 15px;
            color: #555;
            font-size: 13px;
        }

        .messenger-title hr {
            flex: 1;
            border: none;
            border-top: 1px solid #000000;
            margin: 0 10px;
        }

        .messenger-title span {
            white-space: nowrap;
            font-weight: 600;
            font-size: 13px;
        }

        .avatar {
            border: 1px solid #ffff;
        }

        .messenger-list-item tr>td:first-child {
            padding-right: 0;
            width: 55px;
        }

        .messenger-list-item td span {
            color: #000000;
            font-weight: 400;
            font-size: 13px;
        }
    </style>
    <script>
        $(document).on('click', '.messenger-list-item', function () {
            $('.messenger-list-item').removeClass('active'); // Xoá tất cả
            $(this).addClass('active'); // Thêm class vào item vừa click


        });

        document.querySelector('.show-infoSide')?.addEventListener('click', function (e) {
            e.preventDefault();
            const infoView = document.querySelector('.messenger-infoView');
            infoView?.classList.toggle('active-tab');
        });
        document.querySelector('.close-infoSide')?.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector('.messenger-infoView')?.classList.remove('active-tab');
        });

    </script>
    

@endsection