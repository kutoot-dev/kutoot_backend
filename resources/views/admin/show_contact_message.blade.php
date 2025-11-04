@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Contact Message')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Contact Message')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Contact Message')}}</div>
            </div>
          </div>


          <div class="section-body">
            <a class="btn btn-primary" href="{{ route('admin.contact-message') }}"> <i class="fa fa-list" aria-hidden="true"></i> {{__('admin.Contact Message')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <tbody>
                                <tr>
                                    <td>{{__('admin.Name')}}</td>
                                    <td>{{ $contactMessage->name }}</td>
                                </tr>

                                <tr>
                                    <td>{{__('admin.Email')}}</td>
                                    <td>{{ $contactMessage->email }}</td>
                                </tr>

                                <tr>
                                    <td>{{__('admin.Phone')}}</td>
                                    <td>{{ $contactMessage->phone }}</td>
                                </tr>

                                <tr>
                                    <td>{{__('admin.Subject')}}</td>
                                    <td>{{ $contactMessage->subject }}</td>
                                </tr>

                                <tr>
                                    <td>{{__('admin.Message')}}</td>
                                    <td>{{ $contactMessage->message }}</td>
                                </tr>

                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
