@extends('admin.master_layout')
@section('title')
<title>{{__('All Images')}}</title>
@endsection

@section('admin-content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>{{__('All Images')}}</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">
          <a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a>
        </div>
        <div class="breadcrumb-item">{{__('All Images')}}</div>
      </div>
    </div>

    <div class="section-body">
      <a href="{{ route('admin.image-items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('Add New')}}</a>
      <div class="row mt-4">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <div class="row mb-3">
                <div align="right" class="col-12">
                  <form method="GET" action="{{ route('admin.image-items.index') }}">
                    <select class="form-control col-sm-12 col-md-6 col-lg-3" name="type" onchange="this.form.submit()">
                      <option value="">{{ __('All Types') }}</option>
                   @foreach ($types as $type)
    <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
        {{ $type->name }}
    </option>
@endforeach

                    </select>
                  </form>
                </div>
              </div>

              <div class="table-responsive table-invoice">
                <table class="table table-striped" id="dataTable">
                  <thead>
                    <tr>
                      <th>Image</th>
                      <th>Title</th>
                      <th>Type</th>
                      <th>Description</th>
                      <th>{{__('admin.Action')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($imageItems as $item)
                      <tr>
                        <td><img src="{{ asset('storage/' . $item->image_path) }}" width="100" class="admin-img"></td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->type->name ?? '-' }}</td>
                        <td>{{ $item->description }}</td>
                        <td>
                          <a href="{{ route('admin.image-items.edit', $item->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            {{-- <a href="{{ route('admin.image-items.show', $item->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a> --}}
                          <form action="{{ route('admin.image-items.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                          </form>
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
    </div>
  </section>
</div>

<script>
  $(document).ready(function () {
    $('#dataTable').DataTable({
      order: []
    });
  });
</script>
@endsection
