@extends('layout')
@section('title')
    <title>{{__('Compare')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('Compare')}}">
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
                        <h4>{{__('Compare')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('compare') }}">{{__('Compare')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BREADCRUMB END
    ==============================-->

    @if (count($compare_contents) > 0)
    <!--==========================
           COMPARE START
    ===========================-->
    <section id="wsus__compare">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="wsus__compare_list">
                        @php
                            $qty = count($compare_contents);
                            $qty += 1;
                            $percentage = 100 / $qty;
                        @endphp
                        <style>
                            @media (min-width: 1200px) and (max-width: 1399.99px){
                                .wsus__compare_list table tbody tr td {
                                    width: {{ $percentage }}%;
                                }
                            }

                        </style>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr class="d-flex">
                                        <td class="wsus__compare_img">
                                         <p>{{__('Product details')}}</p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                            <td class="wsus__compare_img"><img src="{{ asset($compare_content->options->product->thumb_image) }}" alt="product" class="img-fluid w-100">
                                            </td>
                                        @endforeach


                                    </tr>
                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Product Name')}}</p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                            <td class="wsus__compare_text">
                                                <p><a href="{{ route('product-detail', $compare_content->options->product->slug) }}">{{ $compare_content->options->product->short_name }}</a></p>
                                            </td>
                                        </td>
                                        @endforeach

                                    </tr>
                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Price')}}</p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                            @php
                                                $product = App\Models\Product::where('id',$compare_content->id)->first();
                                            @endphp
                                            <td class="wsus__compare_text">
                                                @php
                                                    $variantPrice = 0;
                                                    $variants = $product->variants->where('status', 1);
                                                    if($variants->count() != 0){
                                                        foreach ($variants as $variants_key => $variant) {
                                                            if($variant->variantItems->where('status',1)->count() != 0){
                                                                $item = $variant->variantItems->where('is_default',1)->first();
                                                                if($item){
                                                                    $variantPrice += $item->price;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $isCampaign = false;
                                                    $today = date('Y-m-d H:i:s');
                                                    $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $product->id])->first();
                                                    if($campaign){
                                                        $campaign = $campaign->campaign;
                                                        if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                            $isCampaign = true;
                                                        }
                                                        $campaignOffer = $campaign->offer;
                                                        $productPrice = $product->price;
                                                        $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                        $totalPrice = $product->price;
                                                        $campaignOfferPrice = $totalPrice - $campaignOfferPrice;
                                                    }

                                                    $totalPrice = $product->price;
                                                    if($product->offer_price != null){
                                                        $offerPrice = $product->offer_price;
                                                        $offer = $totalPrice - $offerPrice;
                                                        $percentage = ($offer * 100) / $totalPrice;
                                                        $percentage = round($percentage);
                                                    }
                                                @endphp

                                                @if ($isCampaign)
                                                    <p class="wsus__compare_price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }}</p>
                                                @else
                                                    @if ($product->offer_price == null)
                                                        <p class="wsus__compare_price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                                    @else
                                                        <p class="wsus__compare_price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $product->offer_price + $variantPrice) }}</p>
                                                    @endif
                                                @endif

                                            </td>
                                        @endforeach



                                    </tr>
                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Rating')}}</p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                            @php
                                                $product = App\Models\Product::where('id',$compare_content->id)->first();
                                                $reviewQty = $product->reviews->where('status',1)->count();
                                                $totalReview = $product->reviews->where('status',1)->sum('rating');
                                                if ($reviewQty > 0) {
                                                    $average = $totalReview / $reviewQty;

                                                    $intAverage = intval($average);

                                                    $nextValue = $intAverage + 1;
                                                    $reviewPoint = $intAverage;
                                                    $halfReview=false;
                                                    if($intAverage < $average && $average < $nextValue){
                                                        $reviewPoint= $intAverage + 0.5;
                                                        $halfReview=true;
                                                    }
                                                }

                                            @endphp
                                            @if ($reviewQty > 0)
                                            <td class="wsus__compare_text">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $reviewPoint)
                                                        <i class="fas fa-star"></i>
                                                    @elseif ($i> $reviewPoint )
                                                        @if ($halfReview==true)
                                                        <i class="fas fa-star-half-alt"></i>
                                                            @php
                                                                $halfReview=false
                                                            @endphp
                                                        @else
                                                        <i class="fal fa-star"></i>
                                                        @endif
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewQty }})</span>
                                            </td>

                                            @endif

                                            @if ($reviewQty == 0)
                                            <td class="wsus__compare_text">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0)</span>
                                            </td>
                                            @endif


                                        @endforeach

                                    </tr>
                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Warranty')}}</p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                            <td class="wsus__compare_text">
                                                <span class="wsus__compare_size">
                                                    @if ($compare_content->options->product->is_warranty == 1)
                                                        {{__('Available')}}
                                                    @else
                                                        {{__('Not Available')}}
                                                    @endif
                                                </span>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Product Return')}}</p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                            <td class="wsus__compare_text">
                                                <span class="wsus__compare_size">
                                                    @if ($compare_content->options->product->is_return == 1)
                                                        {{__('Available')}}
                                                    @else
                                                        {{__('Not Available')}}
                                                    @endif
                                                </span>
                                            </td>
                                        @endforeach
                                    </tr>


                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Item Availability')}}</p>
                                        </td>


                                        @foreach ($compare_contents as $compare_content)
                                            <td class="wsus__compare_text">
                                                @if ($compare_content->options->product->qty > 0)
                                                <span class="wsus__compare_stock">{{__('In Stock')}}</span>
                                                @else
                                                <span class="wsus__compare_stock_out">{{__('Out Of Stock')}}</span>
                                                @endif

                                            </td>
                                        </td>
                                        @endforeach


                                    </tr>

                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Specifications')}}</p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                            @php
                                                $product = App\Models\Product::where('id',$compare_content->id)->first();
                                            @endphp
                                            <td class="wsus__compare_text wsus__compare_des">
                                                @if ($product->is_specification == 1)
                                                    @foreach ($product->specifications as $specification)
                                                        <p><span>{{ $specification->key->key }} : </span> <span class="det">{{ $specification->specification }}</span></p>
                                                    @endforeach
                                                @else
                                                {{__('N/A')}}
                                                @endif

                                            </td>
                                        @endforeach

                                    </tr>
                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('Add To Cart')}} </p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                        <td class="wsus__compare_text">
                                             <a href="{{ route('product-detail', $compare_content->options->product->slug) }}" class="common_btn">{{__('Add to cart')}}</a>
                                        </td>
                                        @endforeach

                                    </tr>
                                    <tr class="d-flex">
                                        <td class="wsus__compare_text">
                                            <p>{{__('remove')}} </p>
                                        </td>
                                        @foreach ($compare_contents as $compare_content)
                                        <td class="wsus__compare_text wsus__del_area">
                                            <a href="{{ route('remove-compare',$compare_content->rowId) }}" class="wsus__compare_del"><i class="far fa-times"></i></a>
                                        </td>
                                        @endforeach


                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @else
    <section id="wsus__cart_view">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="wsus__cart_list cart_empty p-3 p-sm-5 text-center">
                        <p class="mb-4">{{__('your compare list is empty')}}</p>
                        <a href="{{ route('product') }}" class="common_btn"><i class="fal fa-store me-2"></i>{{__('Go to Shop')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--==========================
           COMPARE END
    ===========================-->
@endsection
