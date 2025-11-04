@extends('admin.master_layout')
@section('title')
<title>{{__('Pos')}}</title>
@endsection
@section('style')
<link
    href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;0,800;0,900;1,700&family=Roboto:wght@300;400;500;700&display=swap"
    rel="stylesheet">
<link rel="stylesheet" href="{{ asset('backend/pos/assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('backend/pos/assets/css/respondive.css') }}">
@endsection
@section('admin-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section pos-wrapper-section">
        <div class="section-header">
            <h1>{{__('admin.Pos')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active" style="color: #AE1C9A;"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{__('admin.Pos')}}</div>
            </div>
        </div>
        <div class="section-body">

            <section class="">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-xl-8  product-bg">
                            <div class="row product-main-box">
                                <div class="col-lg-12 product-padding ">
                                    <div class="product-taitel">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="product-btn">
                                                    <a href="{{ route('admin.pos.index') }}">
                                                        <span>
                                                            <svg width="14" height="10" viewBox="0 0 14 10" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 1L1 5M1 5L5 9M1 5L13 5" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </span>
                                                        {{__('admin.Back')}}
                                                    </a>
                                                </div>
                                            </div>
                                        
                                            <div class="col-md-4 pt-1">
                                                <div class="product-taitel">
                                                    <h3>{{__('admin.Product Section')}}</h3>
                                                </div>
                                            </div>
                                        
                                            <div class="col-md-4 pt-2">
                                                    <button style=" background-color: #6777ef; color: #fff;" type="button" class="btn btn-info btn-primary-two" data-toggle="modal"
                                                            data-target="#exampleModalLong-2">
                                                         {{__('admin.Add Product') }}
                                                    </button>
                                            </div>
                                            
                                           
                                            
                                        </div>

                                       
                                        
                                    </div>
                                </div>
                            </div>
                            
                             <!-- Modal -->
                            <div class="modal fade" id="exampleModalLong-2"  role="dialog"
                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-two" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle-1"> {{__('admin.Add New Product') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="modal-from">
                                                <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label>{{__('admin.Thumbnail Image Preview')}}</label>
                                                        <div>
                                                            <img id="preview-img" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                                        </div>
                    
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Thumnail Image')}} <span class="text-danger">*</span></label>
                                                        <input type="file" class="form-control-file"  name="thumb_image" onchange="previewThumnailImage(event)" required>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Short Name')}} <span class="text-danger">*</span></label>
                                                        <input type="text" id="short_name" class="form-control"  name="short_name" value="{{ old('short_name') }}" required>
                                                    </div>
                    
                                                    <div class="form-group col-12">
                                                        <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                                        <input type="text" id="name" class="form-control"  name="name" value="{{ old('name') }}" required>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Slug')}} <span class="text-danger">*</span></label>
                                                        <input type="text" id="slug" class="form-control"  name="slug" value="{{ old('slug') }}">
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                                        <select name="category" class="form-control select2" id="category" required>
                                                            <option value="">{{__('admin.Select Category')}}</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Sub Category')}}</label>
                                                        <select name="sub_category" class="form-control select2" id="sub_category">
                                                            <option value="">{{__('admin.Select Sub Category')}}</option>
                                                        </select>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Child Category')}}</label>
                                                        <select name="child_category" class="form-control select2" id="child_category">
                                                            <option value="">{{__('admin.Select Child Category')}}</option>
                                                        </select>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Brand')}} </label>
                                                        <select name="brand" class="form-control select2" id="brand">
                                                            <option value="">{{__('admin.Select Brand')}}</option>
                                                            @foreach ($brands as $brand)
                                                                <option {{ old('brand') == $brand->id ? 'selected' : '' }} value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.SKU')}} </label>
                                                       <input type="text" class="form-control" name="sku">
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('Price')}} <span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="price" value="{{ old('price') }}" required>
                                                    </div>
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Offer Price')}}</label>
                                                       <input type="text" class="form-control" name="offer_price" value="{{ old('offer_price') }}">
                                                    </div>
                    
                    
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Stock Quantity')}} <span class="text-danger">*</span></label>
                                                       <input type="number" class="form-control" name="quantity" value="{{ old('quantity') }}" required>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Weight')}} <span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="weight" value="{{ old('weight') }}" required>
                                                    </div>
                    
                                                    <div class="form-group col-6">
                                                        <label>{{__('admin.Short Description')}} <span class="text-danger">*</span></label>
                                                        <textarea name="short_description" id="" cols="30" rows="10" class="form-control text-area-5">{{ old('short_description') }}</textarea>
                                                    </div>
                                                    
                                                   <div class="form-group col-6">
                                                        <label>{{__('admin.Long Description')}} <span class="text-danger">*</span></label>
                                                        <textarea name="long_description" id="" cols="30" rows="10" class="form-control text-area-5">{{ old('long_description') }}</textarea>
                                                    </div>

                                                    <div class="form-group col-12">
                                                        <label>{{__('admin.Highlight')}}</label>
                                                        <div>
                                                            <input type="checkbox"name="top_product" id="top_product"> <label for="top_product" class="mr-3" >{{__('admin.Top Product')}}</label>
                    
                                                            <input type="checkbox" name="new_arrival" id="new_arrival"> <label for="new_arrival" class="mr-3" >{{__('admin.New Arrival')}}</label>
                    
                                                            <input type="checkbox" name="best_product" id="best_product"> <label for="best_product" class="mr-3" >{{__('admin.Best Product')}}</label>
                    
                                                            <input type="checkbox" name="is_featured" id="is_featured"> <label for="is_featured" class="mr-3" >{{__('admin.Featured Product')}}</label>
                                                        </div>
                                                    </div>
                    
                                                    <div class="form-group col-12">
                                                        <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-control" required>
                                                            <option value="1">{{__('admin.Active')}}</option>
                                                            <option value="0">{{__('admin.Inactive')}}</option>
                                                        </select>
                                                    </div>
                    
                    
                    
                    
                                                    <div class="form-group col-12">
                                                        <label>{{__('admin.SEO Title')}}</label>
                                                       <input type="text" class="form-control" name="seo_title" value="{{ old('seo_title') }}">
                                                    </div>
                    
                                                    <div class="form-group col-12">
                                                        <label>{{__('admin.SEO Description')}}</label>
                                                        <textarea name="seo_description" id="" cols="30" rows="10" class="form-control text-area-5">{{ old('seo_description') }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                         <button type="submit" class="modal-from-btm-btn">{{__('admin.Save') }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                                                   
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-p-30">
                                <div class="col-lg-12 col-p-0">
                                    <div class="product-categories">
                                        <div class="product-categories-search">
                                            <div class="product-categories-search-main">
                                                <form action="{{ route('admin.pos.product.search') }}" method="GET" id="searchForm">
                                                    <input type="text" name="query" class="form-control" id="exampleFormControlInput1" placeholder="Search products...">
                                                </form>
                                            </div>

                                            <div class="product-categories-main-df">
                                                <button type="button" class="product-categories-search-main-icon" id="searchButton">
                                                    <span>
                                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M18.5 15.7076L20.4217 17.6292C21.1928 18.4004 21.1928 19.6506 20.4217 20.4217C19.6506 21.1928 18.4004 21.1928 17.6293 20.4217L15.7076 18.5M1 9.5C1 4.80558 4.80558 1 9.5 1C14.1944 1 18 4.80558 18 9.5C18 14.1944 14.1944 18 9.5 18C4.80558 18 1 14.1944 1 9.5Z"
                                                                stroke="#232532" stroke-width="1.5" stroke-linecap="round" />
                                                        </svg>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="sub-categories-btn">
                                        <div class="sub-categories-btn-text">
                                            <h6>{{__('admin.Categories')}}</h6>
                                        </div>

                                        <div class="sub-categories-all-btn">
                                            @foreach ($categories as $index => $category)
                                            <a
                                                href="{{ route('admin.pos.category.index',$category->id) }}">{{$category->name}}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-p-30">
                                @foreach ($products as $index => $product)
                                <!-- Modal -->


                                <div class="col-lg-2 col-md-6 col-p-10px">
                                    <div class="product-item">
                                        <div class="product-item-overlay">
                                            <div class="product-btn-item">
                                                <div class="product-item-overlay-btn">
                                                    @if ($product->qty == 0)
                                                        <p>{{__('admin.stock:')}} <span style="color: red;"><b>0</b></span></p>
                                                    @else
                                                        <p>{{__('admin.stock:')}} <span style="color: yellow;"><b>{{$product->qty}}</b></span></p>
                                                    @endif


                                                    <button type="button" class="over-btn" data-toggle="modal"
                                                        data-target="#exampleModalLong{{$product->id}}">
                                                        {{__('admin.Details')}}
                                                    </button>

                                                </div>

                                                <div class="product-item-overlay-btn product-item-overlay-btn-two">
                                                    <a href="{{ route('admin.pos.add.product',$product->id) }}"
                                                        class="over-btn-two">{{__('admin.Select')}}</a>
                                                    {{-- <button  type="button" class="over-btn-two" data-bs-toggle="modal"
                                                            data-bs-target="">
                                                            Select
                                                        </button> --}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-item-img">
                                            <img class="rounded-circle" src="{{ asset($product->thumb_image) }}"
                                                width="100px" height="100px" class="img-fluid">
                                        </div>
                                        <div class="product-item-text">
                                            <p>{{$product->short_name}}</p>

                                            <div class="product-item-text-btm">
                                                @if ($product->offer_price == '')

                                                <span> {{ $setting->currency_icon }}{{ $product->price }} </span>
                                                @else
                                                <span>
                                                    <del>{{ $setting->currency_icon }}{{ $product->price }}</del>
                                                </span>
                                                <span> {{ $setting->currency_icon }}{{ $product->offer_price }} </span>
                                                @endif


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="exampleModalLong{{$product->id}}"
                                    role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-three " role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">{{__('admin.Product Details')}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body modal-body-one">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="place-order-img">
                                                            <img src="{{ asset($product->thumb_image) }}" alt="img">

                                                            {{-- <div class="place-order-img-overlay">
                                                                <div class="icon">
                                                                    <h5>-50%</h5>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 raindo-pd">
                                                        <div class="place-order-text">
                                                            <span>{{ $product->short_name }}</span>
                                                            <h2>{{ $product->name }}</h2>
                                                        </div>

                                                        <div class="place-order-reviews">
                                                            <div class="icon">
                                                                <span>
                                                                    <svg width="80" height="16" viewBox="0 0 80 16"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M8 0L9.79611 5.52786H15.6085L10.9062 8.94427L12.7023 14.4721L8 11.0557L3.29772 14.4721L5.09383 8.94427L0.391548 5.52786H6.20389L8 0Z"
                                                                            fill="#FFA800" />
                                                                        <path
                                                                            d="M24 0L25.7961 5.52786H31.6085L26.9062 8.94427L28.7023 14.4721L24 11.0557L19.2977 14.4721L21.0938 8.94427L16.3915 5.52786H22.2039L24 0Z"
                                                                            fill="#FFA800" />
                                                                        <path
                                                                            d="M40 0L41.7961 5.52786H47.6085L42.9062 8.94427L44.7023 14.4721L40 11.0557L35.2977 14.4721L37.0938 8.94427L32.3915 5.52786H38.2039L40 0Z"
                                                                            fill="#FFA800" />
                                                                        <path
                                                                            d="M56 0L57.7961 5.52786H63.6085L58.9062 8.94427L60.7023 14.4721L56 11.0557L51.2977 14.4721L53.0938 8.94427L48.3915 5.52786H54.2039L56 0Z"
                                                                            fill="#FFA800" />
                                                                        <path
                                                                            d="M72 0L73.7961 5.52786H79.6085L74.9062 8.94427L76.7023 14.4721L72 11.0557L67.2977 14.4721L69.0938 8.94427L64.3915 5.52786H70.2039L72 0Z"
                                                                            fill="#FFA800" />
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            {{-- <div class="text">
                                                                <p>6 Reviews</p>
                                                            </div> --}}
                                                        </div>

                                                        <div class="place-order-del">
                                                            @if ($product->offer_price)
                                                            <span>
                                                                <del>{{ $setting->currency_icon }}{{ $product->offer_price }}</del>
                                                            </span>
                                                            @endif
                                                            <span>{{ $setting->currency_icon }}{{ $product->price }}</span>
                                                        </div>

                                                        <div class="place-order-p">
                                                            <p>
                                                                {!!$product->short_description!!}
                                                            </p>
                                                        </div>

                                                        <div class="availabillity">
                                                            <h2>
                                                                {{__('admin.Availabillity :')}}
                                                                @if ($product->qty == 0)
                                                                <span style="color: red;">{{__('admin.Stock Out')}}</span>
                                                                @else
                                                                <span>{{$product->qty}} {{__('admin.Products Available')}}</span>
                                                                @endif


                                                            </h2>
                                                        </div>
                                                        <form action="{{ route('admin.pos.cart.order.detils',$product->id) }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="selected_values" id="selected_values">
                                                            <div class="pt-3">
                                                                <div class="row">
                                                                    @foreach ($product->activeVariants as $variant)
                                                                        <div class="col-md-6">
                                                                            <label for="size">{{ $variant->name }}</label>
                                                                            <select id="size" name="selectedValues[{{ $variant->id }}]" class="form-control variant-select">
                                                                                <option value="" disabled selected>{{ __('Select') }}</option>
                                                                                @if ($variant->variantItems)
                                                                                    @foreach ($variant->variantItems as $variantItem)
                                                                                        <option value="{{ $variantItem->id }}">{{ $variantItem->name }}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>


                                                            <div class="add-to-cart">
                                                                <div class="col-md-4 mb-3 mt-1">
                                                                    <div class="qty-container">
                                                                        <button class="qty-btn-minus" type="button"><i class="fa fa-minus"></i></button>
                                                                        <input type="number" name="quantity"
                                                                         class="input-qty" value="1" readonly/>
                                                                        <button class="qty-btn-plus" type="button"><i class="fa fa-plus"></i></button>
                                                                    </div>
                                                                </div>


                                                                <div class="add-to-cart-item-modal">
                                                                    <!-- Button trigger modal -->

                                                                    <button type="submit" class="btn-delete">
                                                                        <span>
                                                                            <svg width="14" height="14" viewBox="0 0 14 14"
                                                                                fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_1344_5144)">
                                                                                    <path
                                                                                        d="M8.25309 3.32575C8.25309 4.00929 8.25145 4.69283 8.25418 5.37583C8.25527 5.68424 8.31488 5.74439 8.62382 5.74439C9.96351 5.74603 11.3027 5.74275 12.6423 5.74603C13.2723 5.74767 13.7392 6.05663 13.9241 6.58104C14.2204 7.42098 13.6135 8.24232 12.6757 8.25052C11.5914 8.25982 10.507 8.25271 9.42271 8.25271C9.17665 8.25271 8.93058 8.25216 8.68452 8.25271C8.29082 8.2538 8.25363 8.29154 8.25363 8.69838C8.25309 10.0195 8.25637 11.3412 8.25199 12.6624C8.24981 13.2836 7.92555 13.7544 7.39842 13.9305C6.56399 14.2088 5.75799 13.6062 5.74814 12.6821C5.73776 11.7251 5.74596 10.7687 5.74541 9.81173C5.74541 9.41965 5.74705 9.02812 5.74486 8.63604C5.74322 8.30849 5.68964 8.2538 5.36155 8.25326C4.02186 8.25162 2.68272 8.25545 1.34304 8.25107C0.719125 8.24943 0.249414 7.93008 0.0706069 7.40348C-0.212641 6.57065 0.387757 5.75916 1.30968 5.74658C2.14794 5.73564 2.98620 5.74384 3.82446 5.74384C4.30730 5.74384 4.79013 5.74384 5.27351 5.74384C5.72135 5.74330 5.74541 5.71869 5.74541 5.25716C5.74541 3.95406 5.74268 2.65096 5.74650 1.34786C5.74814 0.720643 6.06201 0.253102 6.58750 0.0704598C7.40826 -0.213893 8.21754 0.370671 8.25199 1.27349C8.25254 1.29154 8.25254 1.31013 8.25254 1.32817C8.25309 1.99531 8.25309 2.66026 8.25309 3.32575Z"
                                                                                        fill="white" />
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                        {{__('admin.Add to Cart')}}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <div class="catagory">
                                                            <p>{{__('admin.Category')}} <span>: {{$product->category_name->name}}</span>
                                                            </p>
                                                            {{-- <p>{{__('admin.Tags :')}}  <span>{{$product->tags}}</span></p> --}}
                                                            <p>{{__('admin.SKU :')}} <span>{{$product->sku}}</span></p>
                                                        </div>

                                                        <div class="social-icon">
                                                            <div class="social-icon-item">
                                                                <div class="text">
                                                                    <p>{{__('admin.Share This') }}</p>
                                                                </div>
                                                                <div class="icon">
                                                                    <a href="#" target="_blank">
                                                                        <span>
                                                                            <svg width="10" height="16"
                                                                                viewBox="0 0 10 16" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path
                                                                                    d="M3 16V9H0V6H3V4C3 1.3 4.7 0 7.1 0C8.3 0 9.2 0.1 9.5 0.1V2.9H7.8C6.5 2.9 6.2 3.5 6.2 4.4V6H10L9 9H6.3V16H3Z"
                                                                                    fill="#3E75B2" />
                                                                            </svg>
                                                                        </span>
                                                                    </a>
                                                                    <a href="#" target="_blank">
                                                                        <span>
                                                                            <svg width="16" height="16"
                                                                                viewBox="0 0 16 16" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path
                                                                                    d="M8 0C3.6 0 0 3.6 0 8C0 11.4 2.1 14.3 5.1 15.4C5 14.8 5 13.8 5.1 13.1C5.2 12.5 6 9.1 6 9.1C6 9.1 5.8 8.7 5.8 8C5.8 6.9 6.5 6 7.3 6C8 6 8.3 6.5 8.3 7.1C8.3 7.8 7.9 8.8 7.6 9.8C7.4 10.6 8 11.2 8.8 11.2C10.2 11.2 11.3 9.7 11.3 7.5C11.3 5.6 9.9 4.2 8 4.2C5.7 4.2 4.4 5.9 4.4 7.7C4.4 8.4 4.7 9.1 5 9.5C5 9.7 5 9.8 5 9.9C4.9 10.2 4.8 10.7 4.8 10.8C4.8 10.9 4.7 11 4.5 10.9C3.5 10.4 2.9 9 2.9 7.8C2.9 5.3 4.7 3 8.2 3C11 3 13.1 5 13.1 7.6C13.1 10.4 11.4 12.6 8.9 12.6C8.1 12.6 7.3 12.2 7.1 11.7C7.1 11.7 6.7 13.2 6.6 13.6C6.4 14.3 5.9 15.2 5.6 15.7C6.4 15.9 7.2 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0Z"
                                                                                    fill="#E12828" />
                                                                            </svg>
                                                                        </span>
                                                                    </a>
                                                                    <a href="#">
                                                                        <span class="pl">
                                                                            <svg width="18" height="14"
                                                                                viewBox="0 0 18 14" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path
                                                                                    d="M17.0722 1.60052C16.432 1.88505 15.7562 2.06289 15.0448 2.16959C15.7562 1.74278 16.3253 1.06701 16.5742 0.248969C15.8985 0.640206 15.1515 0.924742 14.3335 1.10258C13.6933 0.426804 12.7686 0 11.7727 0C9.85206 0 8.28711 1.56495 8.28711 3.48557C8.28711 3.7701 8.32268 4.01907 8.39382 4.26804C5.51289 4.12577 2.9165 2.73866 1.17371 0.604639C0.889175 1.13814 0.71134 1.70722 0.71134 2.34742C0.71134 3.5567 1.31598 4.62371 2.27629 5.26392C1.70722 5.22835 1.17371 5.08608 0.675773 4.83711V4.87268C0.675773 6.5799 1.88505 8.00258 3.48557 8.32268C3.20103 8.39382 2.88093 8.42938 2.56082 8.42938C2.34742 8.42938 2.09845 8.39382 1.88505 8.35825C2.34742 9.74536 3.62784 10.7768 5.15722 10.7768C3.94794 11.7015 2.45412 12.2706 0.818041 12.2706C0.533505 12.2706 0.248969 12.2706 0 12.2351C1.56495 13.2309 3.37887 13.8 5.37062 13.8C11.8082 13.8 15.3294 8.46495 15.3294 3.84124C15.3294 3.69897 15.3294 3.52113 15.3294 3.37887C16.0052 2.9165 16.6098 2.31186 17.0722 1.60052Z"
                                                                                    fill="#3FD1FF" />
                                                                            </svg>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <div class="col-lg-12">
                                    <div class="pagination-btn">
                                        {{ $products->links() }}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-4 col-lg-pl-30px">
                            <div class="row billing-main-box">
                                <div class="col-lg-12 product-padding ">
                                    <div>
                                        <div class="billing-section-taitel">
                                            <h3>{{__('admin.Billing Section') }}</h3>
                                        </div>

                                        <div class="billing-btn-main">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="billing-btn-three">


                                                        <button type="button" class="btn btn-primary-two" data-toggle="modal"
                                                            data-target="#exampleModalLong-1">
                                                            {{__('admin.Add Customer') }}
                                                        </button>


                                                        <!-- Modal -->
                                                        <div class="modal fade" id="exampleModalLong-1"  role="dialog"
                                                            aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-two" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLongTitle-1"> {{__('admin.Add New Customer') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                <div class="modal-from">
                                                                    <form
                                                                        action="{{ route('admin.pos.add.customer') }}"
                                                                        method="post">
                                                                        @csrf
                                                                        <div class="from-item-main">
                                                                        <div class="modal-from-item-d-b">
                                                                            <div class="modal-from-inner">
                                                                            <label for="exampleFormControlInput1" class="form-label">{{__('admin.Full Name') }} <span style="color: red;">*</span></label>
                                                                            <input type="text" class="form-control" name="name" id="exampleFormControlInput1"
                                                                            placeholder=" Name" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-from-item modal-from-item-two">
                                                                            <div class="modal-from-inner">
                                                                            <label for="exampleFormControlInput1" class="form-label">{{__('admin.Email Address') }} <span style="color: red;">*</span></label>
                                                                            <input type="email" class="form-control" name="email" id="exampleFormControlInput5"
                                                                                placeholder="infoyour@gmail.com" required>
                                                                            </div>
                                                                            <div class="modal-from-inner">
                                                                            <label for="exampleFormControlInput1" class="form-label">{{__('admin.Phone Number') }} <span style="color: red;">*</span></label>
                                                                            <input type="text" class="form-control" name="phone" id="exampleFormControlInput4"
                                                                                placeholder="Phone Number" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="from-select-main">
                                                                            <div class="from-select-main-item">
                                                                                <label for="exampleFormControlInput1" class="form-label">{{__('admin.Country') }} <span style="color: red;">*</span></label>
                                                                                <div class="from-select-main">
                                                                                    <select class="form-control select2"  name="country" required>
                                                                                        @foreach ($countries as $key => $country)
                                                                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="from-select-main-item-two">
                                                                            <div class="from-select-main">
                                                                            <div class="from-select-main-item">
                                                                                <label for="exampleFormControlInput1" class="form-label">{{__('admin.State') }} <span style="color: red;">*</span></label>
                                                                                <div class="from-select-main">


                                                                                    <select class="form-control select2" name="state" aria-label="Default select example"@required(true)>
                                                                                        <option value="" disabled selected>{{ __('Select a Country') }}</option>
                                                                                        @php
                                                                                        $stateCount = count($state);
                                                                                        @endphp
                                                                                        @foreach ($state as $key => $state)
                                                                                            @if ($key < $stateCount - 0)
                                                                                                <option value="{{$state->id}}">{{$state->name}}</option>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            </div>
                                                                            <div class="from-select-main">
                                                                            <div class="from-select-main-item">
                                                                                <label for="exampleFormControlInput1" class="form-label">{{__('admin.City') }} <span style="color: red;">*</span></label>
                                                                                <div class="from-select-main">


                                                                                    <select class="form-control select2" name="city" aria-label="Default select example" required>
                                                                                        <option value="" disabled selected>{{ __('Select a Country') }}</option>
                                                                                        @php
                                                                                        $cityCount = count($city);
                                                                                        @endphp
                                                                                        @foreach ($city as $key => $city)
                                                                                            @if ($key < $cityCount - 0)
                                                                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class=" modal-from-item-d-b">
                                                                            <div class="modal-from-inner">
                                                                            <label for="exampleFormControlInput1" class="form-label">{{__('admin.Address') }} <span style="color: red;">*</span></label>
                                                                            <textarea class="form-control" name="address" id="" cols="30" rows="10"></textarea>
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-from-item-check">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio" name="location" id="homeRadio" value="Home">
                                                                                <label class="form-check-label" for="homeRadio">
                                                                                    {{__('admin.Home') }}
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio" name="location" id="officeRadio" value="Office">
                                                                                <label class="form-check-label" for="officeRadio">
                                                                                    {{__('admin.Office') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>

                                                                        <button type="submit" class="modal-from-btm-btn">
                                                                            {{__('admin.Submit') }}
                                                                        </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 d-flex justify-content-end">
                                                    <div class="form-group custom-form-group">
                                                        <form method="post" action="{{ route('admin.pos.update.cart.order') }}">
                                                            @csrf
                                                            @method('PUT')


                                                            <button type="submit" class="btn btn-one">{{__('admin.Update Cart')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="delivery-information">
                                    <div class="delivery-information-taitel">
                                        <h3>{{__('admin.Selected Product')}}</h3>
                                    </div>

                                    <div class="delivery-information-top-item">
                                        <div class="delivery-information-top-inner">
                                            <div class="delivery-information-top-inner-text">
                                                <div class="text-1">
                                                    <p>{{__('Item')}}</p>
                                                </div>
                                                <div class="text-2">
                                                    <p>{{__('QTY')}}</p>
                                                </div>
                                            </div>

                                            <p>{{__('Price')}}</p>
                                            <p>{{__('Action')}}</p>
                                        </div>
                                    </div>

                                    <div class="delivery-information-top-item-two-main">
                                        @php
                                        $grandTotal = 0;
                                        $taxRate = floatval($setting->tax);
                                        $cupon = 0;
                                        @endphp

                                            @foreach ($cart_products as $index => $product)
                                            <div class="delivery-information-top-item-two">
                                                <div class="delivery-information-top-item-two-img">
                                                    <img src="{{ asset($product->card_product->thumb_image) }}" width="50px"
                                                        height="50px" alt="img">

                                                    <div class="text">
                                                        <p>{{$product->card_product->name}}</p>
                                                    </div>
                                                </div>

                                                <div class="count">
                                                    <div class="mainas">
                                                        <p>
                                                            <a
                                                                href="{{ route('admin.pos.cart.decrement.product',$product->id) }}">
                                                                <span>
                                                                    <svg width="14" height="2" viewBox="0 0 14 2"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M13 1L1 1" stroke="black"
                                                                            stroke-width="1.2" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="count-text">
                                                        <input type="number" name="qty_update[{{ $product->id }}]"  value="{{$product->qty}}">
                                                    </div>
                                                    <div class="plus">
                                                        <p>
                                                            <a
                                                                href="{{ route('admin.pos.cart.increment.product',$product->id) }}">
                                                                <span>
                                                                    <svg width="14" height="14" viewBox="0 0 14 14"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M7 1V13M13 7L1 7" stroke="black"
                                                                            stroke-width="1.2" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="price">
                                                    @php
                                                    if($product->card_product->offer_price == '')
                                                    {
                                                    $total = $product->qty * $product->card_product->price;
                                                    $price = $product->card_product->price;
                                                    }else{
                                                    $total = $product->qty * $product->card_product->offer_price;
                                                    $price = $product->card_product->offer_price;
                                                    }
                                                    $grandTotal += $total;
                                                    $tax = ($grandTotal * ($taxRate / 100));
                                                    if ($coupon) {
                                                    $cupon = $coupon->discount;
                                                    }
                                                    $discount =($grandTotal * ($cupon / 100));
                                                    $discountedTotal = $grandTotal-$discount;
                                                    $subTotal = ($discountedTotal + $tax);
                                                    @endphp
                                                    <p>{{ $setting->currency_icon }}{{$price}}</p>
                                                </div>

                                                <div class="action">
                                                    <a href="{{ route('admin.pos.destroy.product',$product->id) }}">
                                                        <span>
                                                            <svg width="19" height="24" viewBox="0 0 19 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M16.7842 6.29297C16.5061 6.29297 16.2393 6.40348 16.0426 6.60018C15.8459 6.79689 15.7354 7.06368 15.7354 7.34187V19.0801C15.7053 19.6105 15.4668 20.1075 15.0719 20.4629C14.677 20.8183 14.1577 21.0033 13.6271 20.9775H5.25686C4.72622 21.0033 4.2069 20.8183 3.81201 20.4629C3.41712 20.1075 3.17867 19.6105 3.14858 19.0801V7.34187C3.14858 7.06368 3.03807 6.79689 2.84136 6.60018C2.64466 6.40348 2.37786 6.29297 2.09968 6.29297C1.82149 6.29297 1.5547 6.40348 1.358 6.60018C1.16129 6.79689 1.05078 7.06368 1.05078 7.34187V19.0801C1.08072 20.167 1.54018 21.1977 2.32853 21.9466C3.11688 22.6954 4.16986 23.1013 5.25686 23.0753H13.6271C14.7141 23.1013 15.7671 22.6954 16.5554 21.9466C17.3438 21.1977 17.8032 20.167 17.8331 19.0801V7.34187C17.8331 7.06368 17.7226 6.79689 17.5259 6.60018C17.3292 6.40348 17.0624 6.29297 16.7842 6.29297Z" />
                                                                <path
                                                                    d="M17.8313 3.14669H13.6357V1.0489C13.6357 0.770713 13.5252 0.503921 13.3285 0.307215C13.1317 0.110509 12.865 0 12.5868 0H6.29339C6.0152 0 5.74841 0.110509 5.5517 0.307215C5.355 0.503921 5.24449 0.770713 5.24449 1.0489V3.14669H1.0489C0.770713 3.14669 0.503921 3.2572 0.307215 3.45391C0.110509 3.65061 0 3.91741 0 4.19559C0 4.47378 0.110509 4.74057 0.307215 4.93727C0.503921 5.13398 0.770713 5.24449 1.0489 5.24449H17.8313C18.1094 5.24449 18.3762 5.13398 18.5729 4.93727C18.7697 4.74057 18.8802 4.47378 18.8802 4.19559C18.8802 3.91741 18.7697 3.65061 18.5729 3.45391C18.3762 3.2572 18.1094 3.14669 17.8313 3.14669ZM7.34228 3.14669V2.0978H11.5379V3.14669H7.34228Z" />
                                                                <path
                                                                    d="M8.39272 16.7813V9.43903C8.39272 9.16085 8.28221 8.89406 8.0855 8.69735C7.8888 8.50065 7.622 8.39014 7.34382 8.39014C7.06563 8.39014 6.79884 8.50065 6.60214 8.69735C6.40543 8.89406 6.29492 9.16085 6.29492 9.43903V16.7813C6.29492 17.0595 6.40543 17.3263 6.60214 17.523C6.79884 17.7197 7.06563 17.8302 7.34382 17.8302C7.622 17.8302 7.8888 17.7197 8.0855 17.523C8.28221 17.3263 8.39272 17.0595 8.39272 16.7813Z" />
                                                                <path
                                                                    d="M12.588 16.7813V9.43903C12.588 9.16085 12.4775 8.89406 12.2808 8.69735C12.0841 8.50065 11.8173 8.39014 11.5391 8.39014C11.2609 8.39014 10.9942 8.50065 10.7974 8.69735C10.6007 8.89406 10.4902 9.16085 10.4902 9.43903V16.7813C10.4902 17.0595 10.6007 17.3263 10.7974 17.523C10.9942 17.7197 11.2609 17.8302 11.5391 17.8302C11.8173 17.8302 12.0841 17.7197 12.2808 17.523C12.4775 17.3263 12.588 17.0595 12.588 16.7813Z" />
                                                            </svg>
                                                        </span>
                                                    </a>

                                                </div>
                                            </div>

                                            @endforeach
                                        </form>
                                    </div>

                                    <div class="apply-promo-code">
                                        <h3>{{__('admin.Apply Promo Code') }}</h3>
                                    </div>

                                    <div class="apply-promo-code-btn-main">
                                        <form action="{{ route('admin.pos.apply.cupon') }}" method="get">

                                            <input type="text" class="form-control" name="coupon"
                                                id="exampleFormControlInput-3" placeholder="QGWRFY98">

                                            <!-- Button trigger modal -->
                                            <button type="submit" class="promo-code-btn">
                                                {{__('admin.Apply') }}
                                            </button>
                                        </form>
                                        <!-- Button trigger modal -->


                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModal"  role="dialog"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel0">{{__('admin.Modal title') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ...
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{__('admin.Close') }}</button>
                                                        <button type="button" class="btn btn-primary">{{__('admin.Save') }}
                                                            {{__('admin.changes') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                    </div>

                                    <div class="sub-total">
                                        <div class="sub-total-item">
                                            <h6>{{__('admin.Sub total :') }}</h6>
                                            <h6>{{__('admin.Discount :') }}</h6>
                                            <h6>{{__('admin.Tax :') }}</h6>
                                        </div>
                                        @if($grandTotal == 0)
                                        <div class="sub-total-inner">
                                            <h6>{{ $setting->currency_icon }}0</h6>
                                            <h6>{{ $setting->currency_icon }}0</h6>
                                            <h6>{{ $setting->currency_icon }}0</h6>
                                        </div>
                                        @else
                                        <div class="sub-total-inner">
                                            <h6>{{ $setting->currency_icon }}{{ $grandTotal }}</h6>
                                            <h6>{{ $setting->currency_icon }}{{$discount}}</h6>
                                            <h6>{{ $setting->currency_icon }}{{$tax}}</h6>
                                        </div>
                                        @endif

                                    </div>

                                    <div class="sub-total-btm">
                                        <div class="sub-total-btm-item">
                                            <h6>{{__('admin.Sub total :') }}</h6>
                                        </div>
                                        @if($grandTotal == 0)
                                        <div class="sub-total-btm-inner">
                                            <h6>{{ $setting->currency_icon }}0</h6>
                                        </div>
                                        @else
                                        <div class="sub-total-btm-inner">
                                            <h6>{{ $setting->currency_icon }}{{$subTotal}}</h6>
                                        </div>

                                        @endif

                                    </div>

                                    <div class="sub-total-btn">
                                        <div class="sub-total-btn-one">


                                            <button type="button" class="cancel-btn" data-toggle="modal"
                                                data-target="#exampleModalLong-3">
                                                {{__('admin.Cancel Order') }}
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="exampleModalLong-3"  role="dialog"
                                                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">

                                                        </div>
                                                        <div class="modal-body modal-body-one">
                                                            <div class="modal-img text-center">
                                                                <img src="{{ asset('backend/pos/assets/images/clear-cart.png') }}"
                                                                    alt="img">
                                                            </div>

                                                            <div class="modal-img-text">
                                                                <h4>{{__('admin.Are you sure') }}</h4>
                                                                <p>{{__('admin.You want to remove all items from cart!!') }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">

                                                            <button type="button" class="no-btn yes-btn"
                                                                data-dismiss="modal">{{__('admin.No') }}</button>

                                                            <a class="no-btn"
                                                                href="{{ route('admin.pos.cart.clear.product') }}">
                                                                {{__('admin.Yes') }}
                                                            </a>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal -->


                                        </div>



                                        <div class="sub-total-btn-two">
                                            <!-- Button trigger modal -->


                                            <button type="button" class="place-order" data-toggle="modal"
                                                data-target="#exampleModal-4" onclick="receiveSubmitView()">
                                                {{__('admin.Place Order') }}
                                            </button>



                                            <!-- Modal -->
                                            <div class="modal fade" id="exampleModal-4" role="dialog"
                                                aria-labelledby="exampleModal-4" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-two modal-dialog-seven ">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel-00">
                                                                {{__('admin.Payment')}}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="modal-from">
                                                                <div class="from-item-main">
                                                                    <form action="{{ route('admin.pos.order.submit')}}" method="post">
                                                                        @csrf
                                                                        <input type="hidden" name="sub_total" value="{{$grandTotal}}">

                                                                        @if($grandTotal == 0)
                                                                        <input type="hidden" name="cupon" value="0">
                                                                        <input type="hidden" name="tax" value="0">
                                                                        <input type="hidden" name="discount" value="0">
                                                                        @else
                                                                        <input type="hidden" name="tax"  value="{{$tax}}">
                                                                        <input type="hidden" name="cupon"  value="{{$couponValue}}">
                                                                        <input type="hidden" name="discount"  value="{{$discount}}">
                                                                        @endif

                                                                        <div class="form-group">
                                                                            <label for="">{{__('admin.Select Customer')}}</label>
                                                                            <select name="customer_id" id="" class="form-control select2" required>
                                                                                <option value="" disabled selected>{{ __('admin.Select a Customer') }}</option>
                                                                                @php
                                                                                $CustomerCount = count($customers);
                                                                                @endphp
                                                                                @foreach ($customers as $key => $customer)
                                                                                    @if ($key < $CustomerCount - 0)
                                                                                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                                                                                    @endif
                                                                                @endforeach
                                                                            </select>
                                                                        </div>


                                                                        <div class="form-group">
                                                                            <label for="">{{__('admin.Add Shipping')}}</label>
                                                                            <select name="shipping_id" id="" class="form-control select2" required>
                                                                                <option value="" disabled selected>{{ __('admin.Select a shipping rule') }}</option>
                                                                                @php
                                                                                $shippingsCount = count($shippings);
                                                                                @endphp
                                                                                @foreach ($shippings as $key => $shipping)
                                                                                    @if ($key < $shippingsCount - 0)
                                                                                        <option value="{{ $shipping->id }}">{{ $shipping->shipping_rule }}</option>
                                                                                    @endif
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="from-select-main">
                                                                            <label for="">{{__('admin.Payment Method')}}</label>
                                                                            <select name="payment_method" id="" class="form-control" required>
                                                                                <option value="" disabled selected>{{ __('Select Payment Method') }}</option>
                                                                                <option value="Cash">{{__('admin.Cash')}}</option>
                                                                                <option value="Cash on Delivery">{{__('admin.Cash on Delivery')}}</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group">
                                                                        <label for="">{{__('admin.Order')}}</label>
                                                                        <select name="order_status" id="" class="form-control" required>
                                                                            <option value="" disabled selected>{{ __('admin.Select Order Status') }}</option>
                                                                            <option value="0">{{__('admin.Pending')}}</option>
                                                                            <option value="1">{{__('admin.In Progress')}}</option>
                                                                            <option value="2">{{__('admin.Delivered')}}</option>
                                                                            <option value="3">{{__('admin.Completed')}}</option>
                                                                            <option value="4">{{__('admin.Declined')}}</option>
                                                                        </select>
                                                                        </div>
                                                                        <button type="submit"class="modal-from-btm-btn">{{ __('admin.Submit')}}</button>
                                                                    </form>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-dialog modal-dialog-six">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel01">
                                                                <span class="icon">
                                                                    <svg width="32" height="32" viewBox="0 0 32 32"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M28.6667 24H24.6667C24.2987 24 24 23.7014 24 23.3334C24 22.9654 24.2987 22.6667 24.6667 22.6667H28.6667C29.7694 22.6667 30.6667 21.7694 30.6667 20.6667V11.3333C30.6667 10.2307 29.7694 9.33334 28.6667 9.33334H3.33334C2.23067 9.33334 1.33334 10.2307 1.33334 11.3333V20.6667C1.33334 21.7694 2.23067 22.6667 3.33334 22.6667H7.33334C7.70135 22.6667 8.00001 22.9654 8.00001 23.3334C8.00001 23.7014 7.70135 24 7.33334 24H3.33334C1.49467 24 0 22.504 0 20.6667V11.3333C0 9.496 1.49467 8 3.33334 8H28.6667C30.5054 8 32 9.496 32 11.3333V20.6667C32 22.504 30.5054 24 28.6667 24Z"
                                                                            fill="black" />
                                                                        <path
                                                                            d="M19.3347 28.0003H11.3346C10.9666 28.0003 10.668 27.7017 10.668 27.3337C10.668 26.9657 10.9666 26.667 11.3346 26.667H19.3347C19.7027 26.667 20.0013 26.9657 20.0013 27.3337C20.0013 27.7017 19.7027 28.0003 19.3347 28.0003Z"
                                                                            fill="black" />
                                                                        <path
                                                                            d="M19.3347 25.3333H11.3346C10.9666 25.3333 10.668 25.0347 10.668 24.6667C10.668 24.2987 10.9666 24 11.3346 24H19.3347C19.7027 24 20.0013 24.2987 20.0013 24.6667C20.0013 25.0347 19.7027 25.3333 19.3347 25.3333Z"
                                                                            fill="black" />
                                                                        <path
                                                                            d="M14.0013 22.6663H11.3346C10.9666 22.6663 10.668 22.3677 10.668 21.9997C10.668 21.6317 10.9666 21.333 11.3346 21.333H14.0013C14.3693 21.333 14.668 21.6317 14.668 21.9997C14.668 22.3677 14.3693 22.6663 14.0013 22.6663Z"
                                                                            fill="black" />
                                                                        <path
                                                                            d="M24.668 9.33335C24.3 9.33335 24.0013 9.03468 24.0013 8.66668V3.33334C24.0013 2.23067 23.104 1.33334 22.0013 1.33334H10.0013C8.89864 1.33334 8.0013 2.23067 8.0013 3.33334V8.66668C8.0013 9.03468 7.70264 9.33335 7.33464 9.33335C6.96664 9.33335 6.66797 9.03468 6.66797 8.66668V3.33334C6.66797 1.496 8.16264 0 10.0013 0H22.0013C23.84 0 25.3347 1.496 25.3347 3.33334V8.66668C25.3347 9.03468 25.036 9.33335 24.668 9.33335Z"
                                                                            fill="black" />
                                                                        <path
                                                                            d="M22.0013 31.9997H10.0013C8.16264 31.9997 6.66797 30.5037 6.66797 28.6664V17.9997C6.66797 17.6317 6.96664 17.333 7.33464 17.333H24.668C25.036 17.333 25.3347 17.6317 25.3347 17.9997V28.6664C25.3347 30.5037 23.84 31.9997 22.0013 31.9997ZM8.0013 18.6663V28.6664C8.0013 29.769 8.89864 30.6664 10.0013 30.6664H22.0013C23.104 30.6664 24.0013 29.769 24.0013 28.6664V18.6663H8.0013Z"
                                                                            fill="black" />
                                                                    </svg>
                                                                </span>
                                                            </h5>


                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                    </div>
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


    </section>
</div>
<script src="{{ asset('backend/pos/assets/js/custom.js')}}"></script>
<script>
    const searchForm = document.getElementById('searchForm');
    const searchButton = document.getElementById('searchButton');

    searchButton.addEventListener('click', function() {
        searchForm.submit();
    });
</script>

<script>
    $(document).ready(function () {
        $('form').submit(function () {
            var selectedValues = {};
            $('.variant-select').each(function () {
                var variantName = $(this).data('variant');
                var selectedValue = $(this).val();
                selectedValues[variantName] = selectedValue;
            });
            $('#selected_values').val(JSON.stringify(selectedValues));
        });
    });
</script>

<script>
    $(document).ready(function () {
        var buttonPlus = $(".qty-btn-plus");
        var buttonMinus = $(".qty-btn-minus");

        buttonPlus.click(function () {
            var $n = $(this).parent(".qty-container").find(".input-qty");
            $n.val(Number($n.val()) + 1);
        });

        buttonMinus.click(function () {
            var $n = $(this).parent(".qty-container").find(".input-qty");
            var amount = Number($n.val());
            if (amount > 0) {
                $n.val(amount - 1);
            }
        });

        // Ensure the quantity value is initially set
        var $initialQty = $(".input-qty");
        $initialQty.val(Number($initialQty.val()));
    });
</script>

<script>
    (function($) {
        "use strict";
        var specification = true;
        $(document).ready(function () {
            $("#name").on("focusout",function(e){
                $("#slug").val(convertToSlug($(this).val()));
            })

            $("#category").on("change",function(){
                var categoryId = $("#category").val();
                if(categoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                        success:function(response){
                            $("#sub_category").html(response.subCategories);
                            var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                            $("#child_category").html(response);
                        },
                        error:function(err){
                            console.log(err);

                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select Sub Category')}}</option>";
                    $("#sub_category").html(response);
                    var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                    $("#child_category").html(response);
                }


            })

            $("#sub_category").on("change",function(){
                var SubCategoryId = $("#sub_category").val();
                if(SubCategoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+SubCategoryId,
                        success:function(response){
                            $("#child_category").html(response.childCategories);
                        },
                        error:function(err){
                            console.log(err);

                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                    $("#child_category").html(response);
                }

            })

            $("#is_return").on('change',function(){
                var returnId = $("#is_return").val();
                if(returnId == 1){
                    $("#policy_box").removeClass('d-none');
                }else{
                    $("#policy_box").addClass('d-none');
                }

            })

            $("#addNewSpecificationRow").on('click',function(){
                var html = $("#hidden-specification-box").html();
                $("#specification-box").append(html);
            })

            $(document).on('click', '.deleteSpeceficationBtn', function () {
                $(this).closest('.delete-specification-row').remove();
            });


            $("#manageSpecificationBox").on("click",function(){
                if(specification){
                    specification = false;
                    $("#specification-box").addClass('d-none');
                }else{
                    specification = true;
                    $("#specification-box").removeClass('d-none');
                }


            })

        });
    })(jQuery);

    function convertToSlug(Text){
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-');
    }

    function previewThumnailImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };

</script>



@endsection
