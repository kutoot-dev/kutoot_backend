@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Email Configuration')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Email Configuration')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
            </div>
          </div>
          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.update-email-configuraion') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.Mail Host')}}</label>
                                    <input type="text" name="mail_host" value="{{ $email->mail_host }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">{{__('admin.Email')}}</label>
                                        <input type="email" name="email" value="{{ $email->email }}" class="form-control">
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.SMTP User Name')}}</label>
                                        <input type="text" name="smtp_username" value="{{ $email->smtp_username }}" class="form-control">
                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('admin.SMTP Password')}}</label>
                                        <input type="text" name="smtp_password" value="{{ $email->smtp_password }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_port">{{__('admin.Mail Port')}}</label>
                                        <input type="text" name="mail_port" value="{{ $email->mail_port }}" class="form-control" id="mail_port">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_encryption">{{__('admin.Mail Encryption')}}</label>
                                        <select name="mail_encryption" id="mail_encryption" class="form-control">
                                            <option {{ $email->mail_encryption=='tls' ? 'selected' :'' }} value="tls">{{__('admin.TLS')}}</option>
                                            <option {{ $email->mail_encryption=='ssl' ? 'selected' :'' }} value="ssl">{{__('admin.SSL')}}</option>
                                        </select>
                                    </div>
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
