@if ($order->order_status == 0)
    <div class="col-xl-12">
        <ul class="progtrckr" data-progtrckr-steps="4">
            <li class="progtrckr_done icon_one check_mark">{{__('Order pending')}}</li>
            <li class="icon_two">{{__('Order Processing')}}</li>
            <li class="icon_three">{{__('on the way')}}</li>
            <li class="icon_four">{{__('Completed')}}</li>
        </ul>
    </div>
@elseif ($order->order_status == 1)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('Order Processing')}}</li>
        <li class="icon_three">{{__('on the way')}}</li>
        <li class="icon_four">{{__('Completed')}}</li>
    </ul>
</div>
@elseif ($order->order_status == 2)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('Order Processing')}}</li>
        <li class="icon_three progtrckr_done check_mark">{{__('on the way')}}</li>
        <li class="icon_four">{{__('Completed')}}</li>
    </ul>
</div>
@elseif ($order->order_status == 3)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('Order Processing')}}</li>
        <li class="icon_three progtrckr_done check_mark">{{__('on the way')}}</li>
        <li class="icon_four progtrckr_done check_mark">{{__('Completed')}}</li>
    </ul>
</div>
@elseif ($order->order_status == 4)
<div class="col-xl-12">
    <ul class="progtrckr" data-progtrckr-steps="4">
        <li class="progtrckr_done icon_one check_mark">{{__('Order pending')}}</li>
        <li class="icon_two progtrckr_done check_mark">{{__('Order Processing')}}</li>
        <li class="icon_three progtrckr_done check_mark">{{__('on the way')}}</li>
        <li class="icon_four red_mark">{{__('Declined')}}</li>
    </ul>
</div>
@endif

<div class="col-xl-12">
    <a href="{{ route('user.order') }}" class="common_btn"><i class="fas fa-chevron-left"></i> {{__('back to order')}}</a>
</div>
