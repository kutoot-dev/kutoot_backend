@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Admin')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Admin')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.admin.index') }}">{{__('admin.Admin')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Admin')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.admin.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Admin')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.admin.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Email')}} <span class="text-danger">*</span></label>
                                    <input type="email" id="slug" class="form-control"  name="email">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Password')}} <span class="text-danger">*</span></label>
                                    <input type="password" id="password" class="form-control"  name="password">
                                </div>

                                {{-- <div class="form-group col-12">
                                    <div class="col-md-6">
                                        <label>Add New Role</label>
                                        <div class="input-group">
                                            <input type="text" id="new_role" class="form-control">
                                            <div class="input-group-append">
                                                <button type="button" id="addRoleBtn" class="btn btn-success">Add</button>
                                            </div>
                                        </div>
                                        <small id="roleError" class="text-danger d-none"></small>
                                </div> --}}


                                  <div class="col-md-6">
                                        <label>Assign Role</label>
                                        <select name="role_id" id="roleSelect" class="form-control">
                                            <option value="">-- Select Role --</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>



                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{__('admin.Active')}}</option>
                                        <option value="0">{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // /* ADD ROLE */
    // const addBtn = document.getElementById('addRoleBtn');
    // const roleInput = document.getElementById('new_role');
    // const roleSelect = document.getElementById('roleSelect');
    // const errorBox = document.getElementById('roleError');

    // addBtn.addEventListener('click', function () {
    //     const roleName = roleInput.value.trim();

    //     if (!roleName) {
    //         errorBox.textContent = 'Role name required';
    //         errorBox.classList.remove('d-none');
    //         return;
    //     }

    //     fetch("{{ route('admin.roles.ajax.store') }}", {
    //         method: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': "{{ csrf_token() }}",
    //             'Content-Type': 'application/json'
    //         },
    //         body: JSON.stringify({ name: roleName })
    //     })
    //     .then(res => res.json())
    //     .then(data => {
    //         if (data.success) {
    //             roleSelect.add(new Option(data.role.name, data.role.id, true, true));
    //             roleInput.value = '';
    //             errorBox.classList.add('d-none');
    //         }
    //     });
    // });

    /* MODULE MOVE */
    // document.getElementById('moveRight').onclick = () => move('allModules','assignedModules');
    // document.getElementById('moveLeft').onclick  = () => move('assignedModules','allModules');

    // function move(from,to){
    //     const f=document.getElementById(from);
    //     const t=document.getElementById(to);
    //     [...f.selectedOptions].forEach(o=>t.appendChild(o));
    // }

});
</script>
@endpush

