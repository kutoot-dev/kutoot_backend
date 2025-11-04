@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Email History')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Email History')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Email History')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.my-profile') }}" class="btn btn-primary"><i class="fas fa-user"></i> {{__('admin.My Profile')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{__('admin.Subject')}}</th>
                                    <th>{{__('admin.Time')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($emails as $index => $email)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $email->subject }}</td>
                                        <td>{{ $email->created_at->format('H:m:A, d M Y') }}</td>
                                        <td>
                                        <a href="javascript:;" data-toggle="modal" data-target="#modelId-{{ $email->id }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </td>

                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

      @foreach ($emails as $index => $email)
      <!-- Modal -->
      <div class="modal fade" id="modelId-{{ $email->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                  <div class="modal-body">
                      <div class="container-fluid">
                          <table class="table table-bordered table-striped">
                            <tr>
                                <td>{{__('admin.Time')}}</td>
                                <td>{{ $email->created_at->format('H:m:A, d M Y') }}</td>
                            </tr>
                            <tr>
                                <td>{{__('admin.Subject')}}</td>
                                <td>{{ $email->subject }}</td>
                            </tr>

                            <tr>
                                <td>{{__('admin.Message')}}</td>
                                <td>{!! clean($email->message) !!}</td>
                            </tr>

                          </table>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                  </div>
              </div>
          </div>
      </div>
      @endforeach
@endsection
