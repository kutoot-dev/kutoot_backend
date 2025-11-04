@if (Cart::instance('default')->count() == 0)
    <h5 class="text-danger text-center">{{('Your Cart is empty')}}</h5>
@else
<ul >
    @php
        $sidebarCartSubTotal = 0;
        $sidebar_cart_contents = Cart::instance('default')->content();
    @endphp
    @foreach ($sidebar_cart_contents as $sidebar_cart_content)
    <li>
        <div class="wsus__cart_img">
            <a href="#"><img src="{{ asset($sidebar_cart_content->options->image) }}" alt="product" class="img-fluid w-100"></a>
            <a class="wsis__del_icon" onclick="sidebarCartItemRemove('{{ $sidebar_cart_content->rowId }}')" href="javascript:;"><i class="fas fa-minus-circle"></i></a>
        </div>
        <div class="wsus__cart_text">
            <a class="wsus__cart_title" href="{{ route('product-detail', $sidebar_cart_content->options->slug) }}">{{ $sidebar_cart_content->name }}</a>
            <p><span>{{ $sidebar_cart_content->qty }} x</span> ${{ $sidebar_cart_content->price }}</p>
        </div>
    </li>

    @php
        $productPrice = $sidebar_cart_content->price;
        $total = $productPrice * $sidebar_cart_content->qty ;
        $sidebarCartSubTotal += $total;
    @endphp
    @endforeach




</ul>
<h5>{{__('Sub Total')}} <span>${{ $sidebarCartSubTotal }}</span></h5>
<div class="wsus__minicart_btn_area">
    <a class="common_btn" href="{{ route('cart') }}">{{__('View Cart')}}</a>
    <a class="common_btn" href="{{ route('user.checkout.billing-address') }}">{{__('Checkout')}}</a>
</div>
@endif
