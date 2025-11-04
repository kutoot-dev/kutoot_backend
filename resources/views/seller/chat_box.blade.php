<div class="card-header">
    <h4>Chat with ibrahim khalil</h4>
</div>
<div class="card-body chat-content">
    <div class="chat-item chat-right" style="">
        <img src="" />
        <div class="chat-details">
            <div class="chat-text">This is message</div>
            <div class="chat-time">2022-12-54</div>
        </div>
    </div>

    <div class="chat-item chat-right" style="">
        <img src="" />
        <div class="chat-details">
            <div class="chat-text">This is message</div>
            <div class="chat-time">2022-12-54</div>
        </div>
    </div>

    <div class="chat-item chat-left" style="">
        <img src="" />
        <div class="chat-details">
            <div class="chat-text">This is message</div>
            <div class="chat-time">2022-12-54</div>
        </div>
    </div>

    <div class="chat-item chat-right" style="">
        <img src="" />
        <div class="chat-details">
            <div class="chat-text">This is message</div>
            <div class="chat-time">2022-12-54</div>
        </div>
    </div>
</div>
<div class="card-footer chat-form">
    <form id="chat-form">
        <input autocomplete="off" type="text" class="form-control" id="customer_message" placeholder="Type message">
        <input type="hidden" id="customer_id" name="customer_id" value="10">
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

    }
</script>

