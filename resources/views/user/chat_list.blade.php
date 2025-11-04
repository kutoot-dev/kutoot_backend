@extends('user.layout')
@section('title')
    <title>{{__('Message')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
      <div class="dashboard_content mt-2 mt-md-0">
        <h3><i class="far fa-star"></i> {{__('Message')}}</h3>
        <div class="wsus__dashboard_review">
          <div class="row">
            <div class="col-xl-4 col-md-5">
              <div class="wsus__chatlist d-flex align-items-start">
                <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                  <h2>{{__('Seller List')}}</h2>
                  <div class="wsus__chatlist_body">
                      @foreach ($sellers as $index => $seller )
                            @php
                                $unRead = App\Models\Message::where(['customer_id' => $auth->id, 'seller_id' => $seller->seller->id, 'send_seller' => $seller->seller->id])->where('customer_read_msg',0)->count();
                            @endphp
                        <button onclick="loadChatBox('{{ $seller->seller->id }}')" class="nav-link seller" id="seller-list-{{ $seller->seller->id }}" data-bs-toggle="pill" data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">
                        <div class="wsus_chat_list_img">
                            <img src="{{ $seller->seller->image ? asset($seller->seller->image) : asset($defaultProfile->image) }}" alt="user" class="img-fluid">
                            <span class="pending {{ $unRead == 0 ? 'd-none' : '' }}" id="pending-{{ $seller->seller->id }}">{{ $unRead }}</span>


                        </div>
                        <div class="wsus_chat_list_text">
                            <h4>{{ $seller->seller->name }}</h4>
                        </div>
                        </button>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-8 col-md-7">
              <div class="wsus__chat_main_area">
                <div class="tab-content" id="v-pills-tabContent">
                  <div class="tab-pane fade" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                    <div id="chat_box">

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


@endsection
