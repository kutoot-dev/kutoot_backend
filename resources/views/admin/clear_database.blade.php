@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Clear Database')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Clear Database')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Clear Database')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <div class="alert alert-warning alert-has-icon">
                            <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                            <div class="alert-body">
                              <div class="alert-title">{{__('admin.Warning')}}</div>
                              {{__('admin.If you want to use the software from scratch, you have to clear database. You do not need to remove the existing data one by one')}}
                            </div>
                          </div>

                          <button class="btn btn-danger" data-toggle="modal" data-target="#clearDatabaseModal">{{__('admin.Clear Database')}}</button>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

      <div class="modal fade" tabindex="-1" role="dialog" id="clearDatabaseModal">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{__('admin.Clear Database Confirmation')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>{{__('admin.Are you really want to clear this database?')}}</p>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <form id="deleteForm" action="{{ route('admin.clear-database') }}" method="POST">
                    @csrf
                    @method("DELETE")
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('admin.Yes, Delete')}}</button>
                </form>
            </div>
          </div>
        </div>
      </div>

@endsection
