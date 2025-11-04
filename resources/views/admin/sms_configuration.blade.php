@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Sms Configuration')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Sms Configuration')}}</h1>

          </div>
          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body sms-body">
                        <div class="alert alert-warning" role="alert">
                          <h4 class="alert-heading"></h4>
                          <p>Before this setup you have to phone number required feature enable on the register page. Otherwise this feature doesn't work. For enable required feature go to <b><a target="_blank" href="{{ route('admin.general-setting') }}">General setting</a></b></p>
                        </div>
                        <h5 class="card_title">{{__('admin.Twilio Configuration')}}</h5>
                        <hr>

                        <form action="{{ route('admin.update-twilio-configuration') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.Account SID')}}</label>
                                    <input type="text" name="account_sid" value="{{ $twilio->account_sid }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.Auth Token')}}</label>
                                    <input type="text" name="auth_token" value="{{ $twilio->auth_token }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.Twilio Phone Number')}}</label>
                                    <input type="text" name="twilio_phone_number" value="{{ $twilio->twilio_phone_number }}" class="form-control">
                                    </div>
                                </div>


                                <div class="form-group col-12">
                                    <label class="custom-switch ">
                                      <input {{ $twilio->enable_register_sms == 1 ? 'checked' : '' }} type="checkbox" name="register_otp" class="custom-switch-input">
                                      <span class="custom-switch-indicator"></span>
                                      <span class="custom-switch-description">{{__('admin.Send Registration OTP')}}</span>
                                    </label>
                                </div>

                                <div class="form-group col-12">
                                    <label class="custom-switch ">
                                      <input {{ $twilio->enable_reset_pass_sms == 1 ? 'checked' : '' }} type="checkbox" name="reset_pass_otp" class="custom-switch-input">
                                      <span class="custom-switch-indicator"></span>
                                      <span class="custom-switch-description">{{__('admin.Forget Password OTP')}}</span>
                                    </label>
                                </div>

                                <div class="form-group col-12">
                                    <label class="custom-switch ">
                                      <input {{ $twilio->enable_order_confirmation_sms == 1 ? 'checked' : '' }} type="checkbox" name="order_confirmation" class="custom-switch-input">
                                      <span class="custom-switch-indicator"></span>
                                      <span class="custom-switch-description">{{__('admin.Order Confirmation')}}</span>
                                    </label>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-success">{{__('admin.Update')}}</button>
                        </form>

                        <hr>
                        <h5 class="card_title">{{__('admin.Biztech Configuration')}}</h5>
                        <hr>

                        <form action="{{ route('admin.update-biztech-configuration') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.Api Key')}}</label>
                                    <input type="text" name="api_key" value="{{ $biztech->api_key }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.Client Id')}}</label>
                                    <input type="text" name="client_id" value="{{ $biztech->client_id }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.Sender Id')}}</label>
                                    <input type="text" name="sender_id" value="{{ $biztech->sender_id }}" class="form-control">
                                    </div>
                                </div>


                                <div class="form-group col-12">
                                    <label class="custom-switch ">
                                      <input {{ $biztech->enable_register_sms == 1 ? 'checked' : '' }} type="checkbox" name="register_otp" class="custom-switch-input">
                                      <span class="custom-switch-indicator"></span>
                                      <span class="custom-switch-description">{{__('admin.Send Registration OTP')}}</span>
                                    </label>
                                </div>

                                <div class="form-group col-12">
                                    <label class="custom-switch ">
                                      <input {{ $biztech->enable_reset_pass_sms == 1 ? 'checked' : '' }} type="checkbox" name="reset_pass_otp" class="custom-switch-input">
                                      <span class="custom-switch-indicator"></span>
                                      <span class="custom-switch-description">{{__('admin.Forget Password OTP')}}</span>
                                    </label>
                                </div>

                                <div class="form-group col-12">
                                    <label class="custom-switch ">
                                      <input {{ $biztech->enable_order_confirmation_sms == 1 ? 'checked' : '' }} type="checkbox" name="order_confirmation" class="custom-switch-input">
                                      <span class="custom-switch-indicator"></span>
                                      <span class="custom-switch-description">{{__('admin.Order Confirmation')}}</span>
                                    </label>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-success">{{__('admin.Update')}}</button>
                        </form>


                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
