@extends('admin.master_layout')
@section('title')
<title>{{__('Edit SMs Template')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Edit SMs Template')}}</h1>
          </div>

        <div class="section-body">
            <a href="{{ route('admin.sms-template') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('SMs Template')}}</a>
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <th>{{__('admin.Variable')}}</th>
                                    <th>{{__('admin.Meaning')}}</th>
                                </thead>
                                <tbody>
                                    @if ($template->id == 1)
                                        <tr>
                                            @php
                                                $name="{{user_name}}";
                                            @endphp
                                            <td>{{ $name }}</td>
                                            <td>{{__('admin.User Name')}}</td>
                                        </tr>

                                        <tr>
                                            @php
                                                $name="{{otp_code}}";
                                            @endphp
                                            <td>{{ $name }}</td>
                                            <td>{{__('OTP')}}</td>
                                        </tr>


                                    @endif

                                    @if ($template->id == 2)
                                        <tr>
                                            @php
                                                $name="{{name}}";
                                            @endphp
                                            <td>{{ $name }}</td>
                                            <td>{{__('admin.User Name')}}</td>
                                        </tr>

                                        <tr>
                                            @php
                                                $name="{{otp_code}}";
                                            @endphp
                                            <td>{{ $name }}</td>
                                            <td>{{__('OTP')}}</td>
                                        </tr>

                                    @endif

                                    @if ($template->id == 3)
                                        <tr>
                                            @php
                                                $name="{{user_name}}";
                                            @endphp
                                            <td>{{ $name }}</td>
                                            <td>{{__('admin.User Name')}}</td>
                                        </tr>

                                        <tr>
                                            @php
                                                $name="{{order_id}}";
                                            @endphp
                                            <td>{{ $name }}</td>
                                            <td>{{__('Order Tracking Id')}}</td>
                                        </tr>

                                    @endif


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.update-sms-template',$template->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="">{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                <textarea name="description" cols="30" rows="10" class="form-control text-area-5">{{ $template->description }}</textarea>
                            </div>
                            <button class="btn btn-success" type="submit">{{__('admin.Update')}}</button>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
