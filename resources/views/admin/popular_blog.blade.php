@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Popular Blog')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Popular Blog')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.blog.index') }}">{{__('admin.Blogs')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Popular Blog')}}</div>
            </div>
          </div>


        <div class="section-body">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.popular-blog.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">{{__('admin.Blog')}}</label>
                                <select name="blog_id" id="" class="form-control select2" required>
                                    <option value="">{{__('admin.Select Blog')}}</option>
                                    @foreach ($blogs as $blog)
                                        <option value="{{ $blog->id }}">{{ $blog->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary">{{__('admin.Save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

          <div class="section-body">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="30%">{{__('admin.Blog')}}</th>
                                    <th width="15%">{{__('admin.Category')}}</th>
                                    <th width="10%">{{__('admin.Image')}}</th>
                                    <th width="15%">{{__('admin.Status')}}</th>
                                    <th width="15%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($popularBlogs as $index => $popularBlog)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $popularBlog->blog->title }}</td>
                                        <td>{{ $popularBlog->blog->category->name }}</td>
                                        <td><img src="{{ asset($popularBlog->blog->image) }}" width="80px" class="rounded-circle" alt=""></td>
                                        <td>
                                            @if($popularBlog->status == 1)
                                            <a href="javascript:;" onclick="changePopularBlogStatus({{ $popularBlog->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @else
                                            <a href="javascript:;" onclick="changePopularBlogStatus({{ $popularBlog->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $popularBlog->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
        $("#deleteForm").attr("action",'{{ url("admin/popular-blog/") }}'+"/"+id)
    }
    function changePopularBlogStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/popular-blog-status/')}}"+"/"+id,
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
