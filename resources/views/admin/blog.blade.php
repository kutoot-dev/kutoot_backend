@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Blog')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Blog')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Blog')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="30%">{{__('admin.Title')}}</th>
                                    <th width="15%">{{__('admin.Category')}}</th>
                                    <th width="10%">{{__('admin.Image')}}</th>
                                    <th width="10%">{{__('admin.Show Homepage')}}</th>
                                    <th width="15%">{{__('admin.Status')}}</th>
                                    <th width="15%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($blogs as $index => $blog)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td><a target="_blank" href="{{ $frontend_url.$blog->slug }}">{{ $blog->title }}</a></td>
                                        <td>{{ $blog->category->name }}</td>
                                        <td><img src="{{ asset($blog->image) }}" width="80px" height="80px" class="rounded-circle" alt=""></td>
                                        <td>
                                            @if ($blog->show_homepage)
                                                <span class="badge badge-success">{{__('admin.Yes')}}</span>
                                            @else
                                            <span class="badge badge-danger">{{__('admin.No')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($blog->status == 1)
                                            <a href="javascript:;" onclick="changeBlogStatus({{ $blog->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @else
                                            <a href="javascript:;" onclick="changeBlogStatus({{ $blog->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @endif
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.blog.edit',$blog->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $blog->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/blog/") }}'+"/"+id)
    }
    function changeBlogStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/blog-status/')}}"+"/"+id,
            success:function(response){
                toastr.success(response)
            },
            error:function(err){
                console.log(err);

            }
        })
    }
</script>
@endsection
