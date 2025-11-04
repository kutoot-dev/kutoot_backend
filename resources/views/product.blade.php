@extends('layout')
@section('title')
    <title>{{ $seoSetting->seo_title }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $seoSetting->seo_description }}">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($banner->image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('Shop')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('product') }}">{{__('shop')}}</a></li>
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
        PRODUCT PAGE START
    ==============================-->
    <section id="wsus__product_page">
        <div class="container">
            <div class="row">
                @if ($shop_page->status == 1)
                    <div class="col-xl-12">
                        <div class="wsus__pro_page_bammer">
                            <img src="{{ asset($shop_page->banner) }}" alt="banner" class="img-fluid w-100">
                            <div class="wsus__pro_page_bammer_text">
                                <div class="wsus__pro_page_bammer_text_center">
                                    <p>{{ $shop_page->header_one }} <span>{{ $shop_page->header_two }}</span></p>
                                    <h5>{{ $shop_page->title_one }}</h5>
                                    <h3>{{ $shop_page->title_two }}</h3>
                                    <a href="{{ $shop_page->link }}" class="add_cart">{{ $shop_page->button_text }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-xl-3 col-lg-4">
                    <div class="wsus__sidebar_filter ">
                        <p>{{__('filter')}}</p>
                        <span class="wsus__filter_icon">
                            <i class="far fa-minus" id="minus"></i>
                            <i class="far fa-plus" id="plus"></i>
                        </span>
                    </div>
                    <form id="searchProductFormId">
                    <div class="wsus__product_sidebar" id="sticky_sidebar">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                              <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    {{__('Filter By Categories')}}
                                </button>
                              </h2>
                              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul>
                                        <li><a class="categoryForSearch" href="javascript:;" data-category="0">{{__('All Categories')}}</a></li>
                                        @foreach ($productCategories as $productCategory)
                                            <li><a class="categoryForSearch" href="javascript:;" data-category="{{ $productCategory->slug }}">{{ $productCategory->name }}</a></li>
                                        @endforeach
                                        <input type="hidden" name="category" value="" id="category_id_for_search">
                                        <input type="hidden" name="page_view" value="grid_view" id="page_view_id">
                                    </ul>
                                </div>
                              </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree3">
                                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree3" aria-expanded="false" aria-controls="collapseThree">
                                      {{__('Filter by Brands')}}
                                  </button>
                                </h2>
                                <div id="collapseThree3" class="accordion-collapse collapse show" aria-labelledby="headingThree3" data-bs-parent="#accordionExample">
                                  <div class="accordion-body">
                                      @foreach ($brands as $brand)
                                        <div class="form-check">
                                            <input name="brands[]" class="form-check-input brand_item" type="checkbox" value="{{ $brand->id }}" id="flexCheckDefault11-{{ $brand->id }}">
                                            <label class="form-check-label" for="flexCheckDefault11-{{ $brand->id }}">
                                            {{ $brand->name }}
                                            </label>
                                        </div>
                                      @endforeach
                                  </div>
                                </div>
                              </div>

                            <div class="accordion-item">
                              <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    {{__('Filter by Price')}}
                                </button>
                              </h2>
                              <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="price_ranger">
                                        <input value="0;{{ $shop_page->filter_price_range }}" type="hidden" name="price_range" id="slider_range" class="flat-slider" />
                                        <button  type="submit" class="common_btn">{{__('filter')}}</button>
                                    </div>
                                </div>
                              </div>
                            </div>
                            @foreach ($variantsForSearch as $variantForSearch)
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree2-{{ $variantForSearch->id }}">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree2-{{ $variantForSearch->id }}" aria-expanded="false" aria-controls="collapseThree">
                                        {{ $variantForSearch->name }}
                                    </button>
                                </h2>

                                <div id="collapseThree2-{{ $variantForSearch->id }}" class="accordion-collapse collapse show" aria-labelledby="headingThree2-{{ $variantForSearch->id }}" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @php
                                            $variantItemsForSearch = App\Models\ProductVariantItem::groupBy('name')->select('name','id')->where('product_variant_name', $variantForSearch->name)->get();
                                        @endphp

                                        @foreach ($variantItemsForSearch as $index => $variantItemForSearch)
                                            <div class="form-check">
                                                <input class="form-check-input variant_item_search" type="checkbox" name="variantItems[]" value="{{ $variantItemForSearch->name }}" id="{{ $variantForSearch->id }}-{{ $variantItemForSearch->id }}-{{ $variantItemForSearch->name }}">
                                                <label class="form-check-label" for="{{ $variantForSearch->id }}-{{ $variantItemForSearch->id }}-{{ $variantItemForSearch->name }}">
                                                {{ $variantItemForSearch->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="row">
                        <div class="col-xl-12 d-none d-md-block mt-md-4 mt-lg-0">
                            <div class="wsus__product_topbar">
                                <div class="wsus__product_topbar_left">

                                    <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                        <button onclick="setPageView('grid_view')" class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill" data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                            <i class="fas fa-th"></i>
                                        </button>
                                        <button onclick="setPageView('list_view')" class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                            <i class="fas fa-list-ul"></i>
                                        </button>
                                    </div>

                                </div>
                                <div class="wsus__topbar_select">
                                    <select class="select_2 shorting_id" name="shorting_id">
                                        <option value="1">{{__('default shorting')}}</option>
                                        <option value="2">{{__('low to high price')}} </option>
                                        <option value="3">{{__('high to low price')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                        <div id="loadeer-hidden-content" class="d-none">
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <div class="preloader">
                                        <img src="{{ asset('user/images/gif.gif') }}" alt="loader" class="img-fluid w-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content load_ajax_response" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">

                                <div class="row">
                                    <div class="col-12 mt-3">
                                        <div class="preloader">
                                            <img src="{{ asset('user/images/gif.gif') }}" alt="loader" class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                <div class="row">
                                    <div class="col-12 mt-3">
                                        <div class="preloader">
                                            <img src="{{ asset('user/images/gif.gif') }}" alt="loader" class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        PRODUCT PAGE END
    ==============================-->




<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            loadProductUsingAjax();
            $(".categoryForSearch").on("click", function(){
                let categoryId = $(this).data('category');
                $("#category_id_for_search").val(categoryId);
                submitSearchForm()
            })

            $("#searchProductFormId").on("submit", function(e){
                e.preventDefault();
                let loader = $("#loadeer-hidden-content").html();
                $('.load_ajax_response').html(loader);
                $.ajax({
                    type: 'get',
                    data: $('#searchProductFormId').serialize(),
                    url: "{{ route('search-product') }}",
                    success: function (response) {
                        $('.load_ajax_response').html(response);
                    },
                    error: function(err) {}
                });
            })

            $(".brand_item").on("click", function(){
                submitSearchForm();
            })

            $(".variant_item_search").on("click", function(){
                submitSearchForm();
            })

            $(".shorting_id").on("change", function(){
                submitSearchForm();
            })



        });
    })(jQuery);

    function loadAjaxProduct(url){
        let loader = $("#loadeer-hidden-content").html();
        $('.load_ajax_response').html(loader);

        let pageView = $("#page_view_id").val();
        var href = new URL(url);
        href.searchParams.set('page_view', pageView);
        query_url = href.toString()
        $.ajax({
            type: 'get',
            url: query_url,
            success: function (response) {
                $('.load_ajax_response').html(response);
            },
            error: function(err) {}
        });
    }

    function setPageView(view){
        $("#page_view_id").val(view);
    }

    function loadProductUsingAjax(){
        let currentURL = window.location.href
        let index = currentURL.indexOf("?");
        currentURL = currentURL.substr(index+1)
        let url = "{{ url('search-product') }}" + "?" + currentURL;
        $.ajax({
            type: 'get',
            url: url,
            success: function (response) {
                $('.load_ajax_response').html(response);
            },
            error: function(err) {}
        });
    }

    function submitSearchForm(){
        let loader = $("#loadeer-hidden-content").html();
        $('.load_ajax_response').html(loader);

        $.ajax({
            type: 'get',
            data: $('#searchProductFormId').serialize(),
            url: "{{ route('search-product') }}",
            success: function (response) {
                $('.load_ajax_response').html(response);
            },
            error: function(err) {}
        });
    }
</script>
@endsection
