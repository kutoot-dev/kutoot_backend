@extends('layout')
@section('title')
    <title>{{ $blog->seo_title }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $blog->seo_description }}">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($blog->banner_image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('Blog')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('blog') }}">{{__('Blog')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BREADCRUMB END
    ==============================-->


    <!--============================
        BLOGS DETAILS START
    ==============================-->
    <section id="wsus__blog_details">
        <div class="container">
            <div class="row">
                <div class="col-xxl-9 col-xl-8 col-lg-8">
                    <div class="wsus__main_blog">
                        <div class="wsus__main_blog_img">
                            <img src="{{ asset($blog->image) }}" alt="blog" class="img-fluid w-100">
                        </div>
                        <p class="wsus__main_blog_header">
                            <span><i class="fas fa-user-tie"></i> by {{ $blog->admin->name }}</span>
                            <span><i class="fal fa-calendar-alt"></i> {{ $blog->created_at->format('d F, Y') }}</span>
                            <span><i class="fal fa-comment-alt-smile"></i> {{ $blog->comments->where('status',1)->count() }} {{__('Comment')}}</span>
                            <span><i class="far fa-eye"></i> {{ $blog->views }} {{__('Views')}}</span>
                        </p>
                        <h2>{{ $blog->title }}</h2>
                        {!! clean($blog->description) !!}


                        <div class="wsus__share_blog">
                            <p>{{__('share')}}:</p>
                            <ul>
                                <li><a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ route('blog-detail', $blog->slug) }}&t={{ $blog->title }}"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a class="twitter" href="https://twitter.com/share?text={{ $blog->title }}&url={{ route('blog-detail', $blog->slug) }}"><i class="fab fa-twitter"></i></a></li>
                                <li><a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ route('blog-detail', $blog->slug) }}&title={{ $blog->title }}"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a class="pinterest" href="https://www.pinterest.com/pin/create/button/?description={{ $blog->title }}&media=&url={{ route('blog-detail', $blog->slug) }}"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>

                        @if ($relatedBlogs->count() != 0)
                        <div class="wsus__related_post">
                            <div class="row">
                                <div class="col-xl-12">
                                    <h5>{{__('related post')}}</h5>
                                </div>
                            </div>
                            <div class="row blog_det_slider">
                                @php
                                    $colorId=1;
                                @endphp
                                @foreach ($relatedBlogs as $index => $relatedBlog)
                                    @php
                                        if($index %4 ==0){
                                            $colorId=1;
                                        }

                                        $color="";
                                        if($colorId==1){
                                            $color="blue";
                                        }else if($colorId==2){
                                            $color="red";
                                        }else if($colorId==3){
                                            $color="orange";
                                        }else if($colorId==4){
                                            $color="green";
                                        }
                                    @endphp
                                    <div class="col-xl-6 col-xxl-4 col-sm-6">
                                        <div class="wsus__single_blog">
                                            <a class="wsus__blog_img" href="{{ route('blog-detail',$relatedBlog->slug) }}">
                                                <img src="{{ asset($relatedBlog->image) }}" alt="blog" class="img-fluid w-100">
                                            </a>
                                            <a class="blog_top {{ $color }}" href="{{ route('blog-by-category', $relatedBlog->category->slug) }}">{{ $relatedBlog->category->name }}</a>
                                            <div class="wsus__blog_text">
                                                <div class="wsus__blog_text_center">
                                                    <a href="{{ route('blog-detail',$relatedBlog->slug) }}">{{ $relatedBlog->title }}</a>
                                                    <p class="date"><span>{{ $relatedBlog->created_at->format('d F, Y') }}</span> {{__('Hosted by')}} {{ $relatedBlog->admin->name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $colorId ++;
                                    @endphp
                                @endforeach

                            </div>
                        </div>
                        @endif
                        <div class="wsus__comment_area">
                            <h4>{{__(' comment ')}} <span>{{ $blog->comments->where('status',1)->count() }}</span></h4>

                            @foreach ($blog->comments->where('status',1) as $blogComment)
                                <div class="wsus__main_comment">
                                    <div class="wsus__comment_img">
                                        <img src="http://www.gravatar.com/avatar/75d23af433e0cea4c0e45a56dba18b30" alt="user" class="img-fluid w-100">
                                    </div>
                                    <div class="wsus__comment_text replay">
                                        <h6>{{ $blogComment->name }} <span>{{ $blogComment->created_at->format('d M, Y') }}</span></h6>
                                        <p>{{ $blogComment->comment }}</p>

                                    </div>
                                </div>
                            @endforeach


                        </div>
                        <div class="wsus__post_comment">
                            <h4>{{__('post a comment')}}</h4>
                            <form id="blogCommentForm">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="wsus__single_com">
                                            <input type="text" name="name" placeholder="{{__('Name')}}">
                                            <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="wsus__single_com">
                                            <input type="email" placeholder="{{__('Email')}}" name="email">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="wsus__single_com">
                                            <textarea cols="3" rows="3" placeholder="{{__('Your Comment')}}" name="comment"></textarea>
                                        </div>
                                    </div>

                                    @if($recaptchaSetting->status==1)
                                        <div class="col-xl-12">
                                            <div class="wsus__single_com mb-3">
                                                <div class="g-recaptcha" data-sitekey="{{ $recaptchaSetting->site_key }}"></div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <button class="common_btn" type="submit">{{__('Submit Comment')}}</button>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="col-xxl-3 col-xl-4 col-lg-4">
                    <div class="wsus__blog_sidebar">
                        <div class="wsus__blog_search">
                            <h4>{{__('search')}}</h4>
                            <form action="{{ route('search-blog') }}">
                                <input type="text" placeholder="Search" name="search">
                                <button type="submit" class="common_btn"><i class="far fa-search"></i></button>
                            </form>
                        </div>
                        <div class="wsus__blog_category">
                            <h4>{{__('Categories')}}</h4>
                            <ul>
                                @foreach ($categories as $category)
                                <li><a href="{{ route('blog-by-category', $category->slug) }}">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @if ($popularPosts->count() > 0)
                        <div class="wsus__blog_post">
                            <h4>{{__('Popular Post')}}</h4>
                            @foreach ($popularPosts as $popularBlog)
                                <div class="wsus__blog_post_single">
                                    <a href="{{ route('blog-detail',$popularBlog->blog->slug) }}" class="wsus__blog_post_img">
                                        <img src="{{ asset($popularBlog->blog->image) }}" alt="blog" class="imgofluid w-100">
                                    </a>
                                    <div class="wsus__blog_post_text">
                                        <a href="{{ route('blog-detail',$popularBlog->blog->slug) }}">{{ $popularBlog->blog->title }}</a>
                                        <p> <span>{{ $popularBlog->blog->created_at->format('d F, Y') }} </span> {{ $popularBlog->blog->comments->where('status',1)->count() }} {{__('Comment')}} </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BLOGS DETAILS END
    ==============================-->


    <script>
        (function($) {
            "use strict";
            $(document).ready(function () {
                $("#blogCommentForm").on('submit', function(e){
                    e.preventDefault();
                    var isDemo = "{{ env('APP_VERSION') }}"
                    if(isDemo == 0){
                        toastr.error('This Is Demo Version. You Can Not Change Anything');
                        return;
                    }
                    $.ajax({
                        type: 'POST',
                        data: $('#blogCommentForm').serialize(),
                        url: "{{ route('blog-comment') }}",
                        success: function (response) {
                            if(response.status == 1){
                                toastr.success(response.message)
                                $("#blogCommentForm").trigger("reset");
                            }
                        },
                        error: function(response) {
                            if(response.responseJSON.errors.name)toastr.error(response.responseJSON.errors.name[0])
                            if(response.responseJSON.errors.email)toastr.error(response.responseJSON.errors.email[0])
                            if(response.responseJSON.errors.comment)toastr.error(response.responseJSON.errors.comment[0])

                            if(!response.responseJSON.errors.name || !response.responseJSON.errors.email || !response.responseJSON.errors.comment){
                                toastr.error("{{__('Please complete the recaptcha to submit the form')}}")
                            }
                        }
                    });
                })


            });
        })(jQuery);

    </script>

@endsection
