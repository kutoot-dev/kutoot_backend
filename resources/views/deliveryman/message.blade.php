@extends('deliveryman.master_layout')
@section('title')
<title>{{__('Message')}}</title>
@endsection
@section('deliveryman-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Message')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('Message')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                            <div class="message p-4 message_box" style="height: 400px; border:1px solid #bbb; overflow: auto;"> 
                            </div>
                            <form id="chat-form">
                                <div class="input-group mt-3">
                                    <input type="hidden" name="order_id" id="order_id" value="{{ $order_id }}">
                                    <input autocomplete="off" type="text" class="form-control" name="message" id="message" placeholder="Text message">
                                    <div class="input-group-prepend">
                                        <button type="submit" class="btn btn-primary btn-lg chat-form-btn"><i class="far fa-paper-plane"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
          </div>
        </section>
      </div>

<script>
        (function($) {
        "use strict";
        $(document).ready(function () {
            getMessage();
            scrollToBottomFunc();
            function getMessage(){
                let order_id=$('#order_id').val();
                $.ajax({
                type:"get",
                url: "{{ url('deliveryman/get-message-with-customer') }}/"+order_id,
                success:function(response){
                    $(".message_box").html(response);
                    scrollToBottomFunc();
                    },
                    error:function(err){
                    }
                })
            };
            setInterval(getMessage, 5000);
            
            $("#chat-form").on("submit", function(event){
                event.preventDefault();
                var isDemo = "{{ env('APP_VERSION') }}"
                if(isDemo == 0){
                    toastr.error('This Is Demo Version. You Can Not Change Anything');
                    return;
                }
                let message = $("#message").val();
                let order_id = $("#order_id").val();
                $("#message").val('');
                if(message){
                    $.ajax({
                        type:"get",
                        data : {message: message , order_id : order_id},
                        url: "{{ route('deliveryman.sent-message-to-customer') }}",
                        success:function(response){
                            getMessage();
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
            $('.message_box').animate({
                scrollTop: $('.message_box').get(0).scrollHeight
            }, 50);
        }
    </script>
@endsection
