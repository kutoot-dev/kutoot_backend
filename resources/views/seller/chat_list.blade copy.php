@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Message')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Message')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Message')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row justify-content-center">
              <div class="col-12 col-sm-6 col-lg-4">
                <div class="card">
                  <div class="card-header">
                    <h4>{{__('admin.Customer List')}}</h4>
                  </div>
                  <div class="card-body seller_chat_list">
                    <ul class="list-unstyled list-unstyled-border">
                        @foreach ($customers as $customer)
                            @php
                                $unRead = App\Models\Message::where(['seller_id' => $auth->id, 'customer_id' => $customer->customer->id, 'send_customer' => $customer->customer->id])->where('seller_read_msg',0)->count();
                            @endphp
                            <li id="customer-list-{{ $customer->customer->id }}" class="media mt-2" onclick="loadChatBox('{{ $customer->customer->id }}')" style="cursor: pointer">
                                <img alt="image" class="mr-3 ml-3 rounded-circle" width="50" src="{{ $customer->customer->image ? asset($customer->customer->image) : asset($defaultProfile->image) }}">
                                <span class="pending {{ $unRead == 0 ? 'd-none' : '' }}" id="pending-{{ $customer->customer->id }}">{{ $unRead }}</span>
                                <div class="media-body mt-4">
                                    <div class="font-weight-bold">{{ $customer->customer->name }}</div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-lg-8">
                <div class="card chat-box" id="mychatbox">

                </div>
              </div>

            </div>
          </div>
        </section>
      </div>
@endsection
