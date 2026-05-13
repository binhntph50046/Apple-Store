
@extends('client.layouts.app')

@section('title', 'Địa chỉ cửa hàng')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .location-section {
            max-width: 700px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            padding: 32px 24px 24px 24px;
        }
        .location-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #222;
            letter-spacing: 1px;
        }
        .map-container {
            width: 100%;
            min-height: 350px;
            height: 400px;
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: box-shadow 0.2s;
        }
        .map-container:hover {
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        }
        @media (max-width: 768px) {
            .location-section {
                padding: 16px 6px;
            }
            .location-title {
                font-size: 1.2rem;
            }
            .map-container {
                min-height: 220px;
                height: 220px;
            }
        }
    </style>
    <section class="location-section">
        <h2 class="location-title">📍 Vị trí cửa hàng Apple Store</h2>
        <iframe class="map-container"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.3901752232728!2d105.7818723734334!3d21.017068588183584!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454ab43c0c4db%3A0xdb6effebd6991106!2sKeangnam%20Landmark%20Tower%2072!5e0!3m2!1sen!2sus!4v1750345578381!5m2!1sen!2sus"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </section>
@endsection