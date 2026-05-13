@extends('client.layouts.app')
@section('title', 'Bài viết - Apple Store')

@section('content')
    <!-- Shop Banner -->
    <div class="shop-banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 data-aos="fade-up">Bài viết</h1>
                    <!-- Breadcrumbs -->
                    <div class="breadcrumbs" data-aos="fade-up" data-aos-delay="200">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Bài viết</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Start Blog Section -->
    <div class="blog-section">
        <div class="container">
            <div class="row">
                @foreach($blogs as $blog)
                    <div class="col-12 col-sm-6 col-md-4 mb-5">
                        <div class="post-entry">
                            <a href="{{ route('blog.show',$blog->slug) }}" class="post-thumbnail"><img src="{{ asset($blog->image ?? 'uploads/default/default.jpg') }}" alt="Image" class="img-fluid"></a>
                            <div class="post-content-entry">
                                <h3><a href="{{ route('blog.show', $blog->slug) }}">{{ Str::limit($blog->title, 90) }}</a></h3>
                                <div class="meta">
                                    <span>{{ $blog->created_at->diffForHumans() }}</span> 
                                    {{-- <span>on <a href="#">{{ $blog->created_at->format('M d, Y') }}</a></span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Blog Section -->
@endsection