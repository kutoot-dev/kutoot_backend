@extends('admin.master_layout')
@section('title')
<title>{{__('Sms Template')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Sms Template')}}</h1>
          </div>
          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-invoice">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{__('admin.SN')}}</th>
                                        <th>{{__('Sms Template')}}</th>
                                        <th>{{__('admin.Action')}}</th>
                                      </tr>
                                </thead>
                                <tbody>
                                    @foreach ($templates as $index => $item)
                                        <tr>
                                            <td>{{ ++$index }}</td>
                                            <td>{{ ucfirst($item->name) }}</td>
                                            <td>
                                                <a  href="{{ route('admin.edit-sms-template',$item->id) }}" class="btn btn-success btn-sm"><i class="fas fa-edit"></i></a>
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
@endsection
