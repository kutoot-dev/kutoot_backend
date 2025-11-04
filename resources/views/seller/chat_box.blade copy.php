<div class="card-header">
    <h4>{{__('admin.Chat with')}} {{ $customer->name }}</h4>
  </div>
  <div class="card-body chat-content">
    @foreach ($messages as $msg_index => $message)
        @if ($message->send_seller == $auth->id)
            <div class="chat-item chat-right" style="">
                <img src="{{ $auth->image ? asset($auth->image) : asset($defaultProfile->image) }}">
                <div class="chat-details">
                    <div class="chat-text">{{ $message->message }}</div>
                    <div class="chat-time">{{ $message->created_at->format('d F, Y, H:i A') }}</div>
                </div>
            </div>
        @else
            <div class="chat-item chat-left" style="">
                <img src="{{ $customer->image ? asset($customer->image) : asset($defaultProfile->image) }}">
                <div class="chat-details">
                    <div class="chat-text">{{ $message->message }}</div>
                    <div class="chat-time">{{ $message->created_at->format('d F, Y, H:i A') }}</div>
                </div>
            </div>
        @endif
    @endforeach
  </div>
  <div class="card-footer chat-form">
    <form id="chat-form">
      <input autocomplete="off" type="text" class="form-control" id="customer_message" placeholder="{{__('admin.Type message')}}">
      <input type="hidden" id="customer_id" name="customer_id" value="{{ $customer->id }}">
      <button type="submit" class="btn btn-primary">
        <i class="far fa-paper-plane"></i>
      </button>
    </form>
  </div>


<script>

    (function($) {
    "use strict";
    $(document).ready(function () {
        scrollToBottomFunc()
        $("#chat-form").on("submit", function(event){
            event.preventDefault()
            var isDemo = "{{ env('APP_VERSION') }}"
            if(isDemo == 0){
                toastr.error('This Is Demo Version. You Can Not Change Anything');
                return;
            }

            let customer_message = $("#customer_message").val();
            let customer_id = $("#customer_id").val();
            $("#customer_message").val('');
            if(customer_message){
                $.ajax({
                    type:"get",
                    data : {message: customer_message , customer_id : customer_id},
                    url: "{{ route('seller.send-message') }}",
                    success:function(response){
                        $(".chat-content").html(response);
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
        $('.chat-content').animate({
            scrollTop: $('.chat-content').get(0).scrollHeight
        }, 50);
    }
</script>

