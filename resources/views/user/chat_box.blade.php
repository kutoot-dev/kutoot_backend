<div class="wsus__chat_area">
    <div class="wsus__chat_area_header">
      <h2>{{__('Chat with')}} {{ $seller->name }}</h2>
    </div>
    <div class="wsus__chat_area_body">
        @foreach ($messages as $msg_index => $message)
            @if($message->send_customer == $auth->id)
                <div class="wsus__chat_single single_chat_2">
                    <div class="wsus__chat_single_img">
                    <img src="{{ $auth->image ? asset($auth->image) : asset($defaultProfile->image) }}" alt="user" class="img-fluid">
                    </div>
                    <div class="wsus__chat_single_text">
                    <p>{{ $message->message }}</p>
                    <span>{{ $message->created_at->format('d F, Y, H:i A') }}</span>
                    </div>
                </div>
            @else
                <div class="wsus__chat_single">
                    <div class="wsus__chat_single_img">
                    <img src="{{ $seller->image ? asset($seller->image) : asset($defaultProfile->image) }}" alt="user" class="img-fluid">
                    </div>
                    <div class="wsus__chat_single_text">
                    <p>{{ $message->message }}</p>
                    <span>{{ $message->created_at->format('d F, Y, H:i A') }}</span>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <div class="wsus__chat_area_footer">
      <form id="customerToSellerMsgForm">
        <input type="text" placeholder="{{__('Type Message')}}" id="seller_message" autocomplete="off">
        <input type="hidden" name="seller_id" id="seller_id" value="{{ $seller->id }}">
        <button type="submit"><i class="fas fa-paper-plane"></i></button>
      </form>
    </div>
</div>

<script>

    (function($) {
    "use strict";
    $(document).ready(function () {
        scrollToBottomFunc()
        $("#customerToSellerMsgForm").on("submit", function(event){
            event.preventDefault()
            var isDemo = "{{ env('APP_VERSION') }}"
            if(isDemo == 0){
                toastr.error('This Is Demo Version. You Can Not Change Anything');
                return;
            }

            let seller_msg = $("#seller_message").val();
            let seller_id = $("#seller_id").val();
            $("#seller_message").val('');
            if(seller_msg){
                $.ajax({
                    type:"get",
                    data : {message: seller_msg , seller_id : seller_id},
                    url: "{{ route('user.send-message') }}",
                    success:function(response){
                        $(".wsus__chat_area_body").html(response)
                        scrollToBottomFunc()
                    },
                    error:function(err){
                    }
                })
            }

        })
    });
  })(jQuery);

    function scrollToBottomFunc() {
        $('.wsus__chat_area_body').animate({
            scrollTop: $('.wsus__chat_area_body').get(0).scrollHeight
        }, 50);
    }
</script>
