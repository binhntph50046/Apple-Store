@extends('client.layouts.app')

@section('title', 'Câu hỏi thường gặp')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .faq-section {
        background-color: #f8f9fa;
        padding: 60px 0;
    }

    .faq-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #0d6efd; /* Màu xanh nhạt chủ đạo */
    }

    .accordion-item {
        border: none;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .accordion-button {
        font-weight: 600;
        color: #0d1e39; /* Màu chữ đậm phù hợp với tông Apple Store */
        background-color: #ffffff;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e6f0ff; /* Màu mở ra */
        color: #0d6efd;
    }

    .accordion-body {
        color: #333;
        line-height: 1.6;
        background-color: #f9f9f9;
    }

    .accordion-button::after {
        transition: transform 0.3s ease;
    }

    .accordion-button:not(.collapsed)::after {
        transform: rotate(180deg);
    }
</style>

<section class="faq-section">
    <div class="container">
        <h2 class="faq-title text-center mb-5">Câu hỏi thường gặp</h2>

        @if ($faqs->isEmpty())
            <div class="alert alert-info text-center">
                Hiện chưa có câu hỏi nào được đăng.
            </div>
        @else
            <div class="accordion" id="faqAccordion">
                @foreach ($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button collapsed" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $index }}"
                                    aria-expanded="false" 
                                    aria-controls="collapse{{ $index }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" 
                             class="accordion-collapse collapse" 
                             aria-labelledby="heading{{ $index }}" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const accordion = document.getElementById('faqAccordion');
        const buttons = accordion.querySelectorAll('.accordion-button');

        buttons.forEach((button) => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = button.getAttribute('data-bs-target');
                const target = document.querySelector(targetId);
                const collapseInstance = bootstrap.Collapse.getInstance(target) || new bootstrap.Collapse(target, { toggle: false });

                // Kiểm tra tất cả các mục và đóng các mục khác
                accordion.querySelectorAll('.accordion-collapse').forEach((item) => {
                    if (item !== target && item.classList.contains('show')) {
                        const otherCollapse = bootstrap.Collapse.getInstance(item);
                        if (otherCollapse) otherCollapse.hide();
                    }
                });

                // Toggle mục hiện tại
                if (target.classList.contains('show')) {
                    collapseInstance.hide();
                } else {
                    collapseInstance.show();
                }
            });
        });
    });
</script>
@endsection