@extends('client.layouts.app')

@section('title', $blog->title)

@section('content')
    <!-- Tiêu đề blog -->
    <div class="blog-hero py-5 bg-light">
        <div class="container-xl">
            <div class="row justify-content-center text-center">
                <div class="col-lg-10">
                    <h1 class="display-5 fw-bold mb-3" data-aos="fade-up">{{ $blog->title }}</h1>
                    <div class="meta text-muted mb-4" data-aos="fade-up" data-aos-delay="200">
                        <span>Bởi <strong>{{ $blog->author->name ?? 'Không rõ' }}</strong></span> |
                        <span>{{ $blog->created_at->format('d/m/Y') }}</span> |
                        <span>{{ $blog->category->name ?? 'Không phân loại' }}</span>
                    </div>
                    {{-- <figure class="blog-hero-image text-center">
                        <img src="{{ $blog->image ? asset('uploads/blogs/' . basename($blog->image)) : asset('images/placeholder.jpg') }}"
                             class="img-fluid rounded shadow-sm"
                             alt="{{ $blog->title }}">
                    </figure> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Nội dung và sidebar -->
    <div class="blog-section py-5">
        <div class="container-xl">
            <div class="row g-4">
                <!-- Nội dung chính -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4 bg-white">
                            <div class="blog-content">
                                {!! $blog->content !!}
                            </div>
                            <hr>
                            <!-- Chia sẻ -->
                            <div class="social-share mt-4 p-4 bg-light rounded">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-share-alt me-2"></i>Chia sẻ bài viết:
                                </h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                       class="btn btn-facebook d-flex align-items-center px-3 py-2 rounded-pill" 
                                       target="_blank"
                                       style="background: #1877f2; color: white; text-decoration: none;">
                                        <i class="fab fa-facebook-f me-2"></i>
                                        <span>Facebook</span>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}"
                                       class="btn btn-twitter d-flex align-items-center px-3 py-2 rounded-pill" 
                                       target="_blank"
                                       style="background: #1da1f2; color: white; text-decoration: none;">
                                        <i class="fab fa-twitter me-2"></i>
                                        <span>Twitter</span>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($blog->title) }}"
                                       class="btn btn-linkedin d-flex align-items-center px-3 py-2 rounded-pill" 
                                       target="_blank"
                                       style="background: #0077b5; color: white; text-decoration: none;">
                                        <i class="fab fa-linkedin-in me-2"></i>
                                        <span>LinkedIn</span>
                                    </a>
                                    <button class="btn btn-copy d-flex align-items-center px-3 py-2 rounded-pill" 
                                            onclick="copyToClipboard('{{ url()->current() }}')"
                                            style="background: #6c757d; color: white; border: none;">
                                        <i class="fas fa-copy me-2"></i>
                                        <span>Copy Link</span>
                                    </button>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-heart text-danger me-1"></i>
                                        Hãy chia sẻ nếu bạn thấy hữu ích!
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <!-- Bài viết liên quan -->
                    @if (!empty($relatedBlogs) && $relatedBlogs->count())
                        <div style="margin-top: 23px"  class="card border-0 shadow-sm">
                            <div class="card-body p-4 bg-white">
                                <h5 class="fw-bold mb-3">Bài viết liên quan</h5>
                                @foreach($relatedBlogs as $related)
                                    <div class="d-flex mb-3 align-items-center">
                                        <a href="{{ route('blog.show', $related->slug) }}" class="me-3">
                                            <img src="{{ $related->image ? asset('uploads/blogs/' . basename($related->image)) : asset('images/placeholder.jpg') }}"
                                                 alt="{{ $related->title }}"
                                                 class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                        </a>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('blog.show', $related->slug) }}"
                                                   class="text-dark text-decoration-none">
                                                    {{ Str::limit($related->title, 50) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">{{ $related->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- CSS để fix vấn đề fixed navbar -->
    <style>
        .blog-hero {
            padding-top: calc(var(--navbar-height, 80px) + 3rem) !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .blog-hero {
                padding-top: calc(var(--navbar-height, 70px) + 2rem) !important;
            }
        }

        /* Custom styling cho social share buttons */
        .btn-facebook:hover { background: #166fe5 !important; transform: translateY(-2px); }
        .btn-twitter:hover { background: #1991db !important; transform: translateY(-2px); }
        .btn-linkedin:hover { background: #006396 !important; transform: translateY(-2px); }
        .btn-copy:hover { background: #5a6268 !important; transform: translateY(-2px); }
        
        .btn-facebook, .btn-twitter, .btn-linkedin, .btn-copy {
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Hover effects cho sidebar */
        .hover-card:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-link:hover {
            color: #0d6efd !important;
        }

        /* Đảm bảo sidebar cards có chiều cao đồng đều */
        .col-lg-4 .card {
            height: fit-content;
        }

        /* Animation cho icons */
        .card-body i {
            transition: color 0.3s ease;
        }

        .card-body:hover i {
            transform: scale(1.1);
        }
    </style>

    <!-- Script điều chỉnh padding-top động và copy link -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function adjustBlogHeroPadding() {
                const navbar = document.querySelector('.custom-navbar');
                const blogHero = document.querySelector('.blog-hero');
                
                if (navbar && blogHero) {
                    const navbarHeight = navbar.offsetHeight;
                    // Set CSS custom property
                    document.documentElement.style.setProperty('--navbar-height', navbarHeight + 'px');
                    // Also set direct style as fallback
                    blogHero.style.paddingTop = `${navbarHeight + 48}px`;
                }
            }
            
            // Adjust on load
            adjustBlogHeroPadding();
            
            // Adjust on resize
            window.addEventListener('resize', adjustBlogHeroPadding);
        });

        // Function to copy link to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const btn = event.target.closest('.btn-copy');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i><span>Đã copy!</span>';
                btn.style.background = '#28a745';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '#6c757d';
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Không thể copy link. Vui lòng copy thủ công: ' + text);
            });
        }
    </script>
  
@endsection