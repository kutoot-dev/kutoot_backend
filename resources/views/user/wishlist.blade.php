@extends('user.layout')
@section('title')
    <title>{{__('Wishlist')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
        <div class="dashboard_content mt-2 mt-md-0">
            <h3><i class="far fa-heart"></i> {{__('Wishlist')}}</h3>
            <div class="wsus__dashboard_wishlist">
                <div class="row">
                    <div class="col-12">
                        <div class="wsus__cart_list wishlist">
                            <div class="table-responsive">
                                <table>
                                    <tbody>
                                        <tr class="d-flex">
                                            <th class="wsus__pro_img">
                                                {{__('Image')}}
                                            </th>

                                            <th class="wsus__pro_name">
                                                {{__('product')}}
                                            </th>

                                            <th class="wsus__pro_tk">
                                                {{__('price')}}
                                            </th>

                                            <th class="wsus__pro_icon">
                                                {{__('action')}}
                                            </th>
                                        </tr>
                                        @foreach ($wishlists as $wishlist)
                                            @php
                                                $product = $wishlist->product;
                                            @endphp
                                        <tr class="d-flex">
                                            <td class="wsus__pro_img"><img src="{{ asset($product->thumb_image) }}" alt="product" class="img-fluid w-100">
                                                <a href="{{ route('user.remove-wishlist',$wishlist->id) }}"><i class="far fa-times"></i></a>
                                            </td>

                                            <td class="wsus__pro_name">
                                                <p><a href="{{ route('product-detail', $product->slug) }}">{{ $product->short_name }}</a></p>
                                            </td>


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
                                                }else{
                                                    $totalPrice = $product->price;
                                                    if($product->offer_price != null){
                                                        $offerPrice = $product->offer_price;
                                                        $offer = $totalPrice - $offerPrice;
                                                        $percentage = ($offer * 100) / $totalPrice;
                                                        $percentage = round($percentage);
                                                    }
                                                }
                                        @endphp

                                            <td class="wsus__pro_tk">
                                                @if ($isCampaign)
                                                    <h6>{{ $setting->currency_icon }}{{ $campaignOfferPrice + $variantPrice }}</h6>
                                                @else
                                                    @if ($product->offer_price == null)
                                                        <h6>{{ $setting->currency_icon }}{{ $totalPrice + $variantPrice }}</h6>
                                                    @else
                                                        <h6>{{ $setting->currency_icon }}{{ $product->offer_price + $variantPrice }}</h6>
                                                    @endif
                                                @endif

                                            </td>

                                            <td class="wsus__pro_icon">
                                                <a class="common_btn" href="{{ route('product-detail', $product->slug) }}">{{__('View Product')}}</a>
                                            </td>
                                        </tr>

                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="pagination">
                            {{ $wishlists->links('custom_paginator') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
