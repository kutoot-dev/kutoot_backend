{{-- {{ dd($roles) }} --}}
@extends('admin.master_layout')

@section('title')
<title>Create Admin</title>
@endsection

@section('admin-content')
<div class="main-content">
<section class="section">

<div class="section-header">
    <h1>Create Role</h1>
</div>



<div class="section-body">
<div class="card">
<div class="card-body">

<form action="{{ route('admin.user.store') }}" method="POST">
@csrf

{{-- ================= ROLE SECTION ================= --}}
<div class="row">
    <div class="col-md-6">
        <label>Add New Role</label>
        <div class="input-group">
            <input type="text" id="new_role" class="form-control">
            <div class="input-group-append">
                <button type="button" id="addRoleBtn" class="btn btn-success">Add</button>
            </div>
        </div>
        <small id="roleError" class="text-danger d-none"></small>
    </div>

    <div class="col-md-6">
        <label>Select Role</label>
        <select name="role_id" id="roleSelect" class="form-control">
            <option value="">-- Select Role --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<hr>

{{-- ================= USER SECTION ================= --}}
{{-- <div class="row">
     <div class="col-md-6">
        <label>Select Role</label>
        <select name="role_id" id="roleSelect" class="form-control">
            <option value="">-- Select Role --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
</div> --}}

<hr>

{{-- ================= MODULE SECTION ================= --}}
<div class="row">
    <div class="col-md-6">
        <label>All Modules</label>
        <select id="allModules" class="form-control" multiple size="8">
            @foreach($modules as $module)
                <option value="{{ $module->id }}">{{ $module->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-1 d-flex flex-column justify-content-center align-items-center">
        <button type="button" id="moveRight" class="btn btn-primary mb-2">→</button>
        <button type="button" id="moveLeft" class="btn btn-secondary">←</button>
    </div>

    <div class="col-md-5">
        <label>Assigned Modules</label>
        <select name="modules[]" id="assignedModules" class="form-control" multiple size="8">
        </select>
    </div>
</div>



<div class="text-right mt-4">
    <button class="btn btn-primary">
        <i class="fas fa-save"></i> Save
    </button>
</div>

</form>

</div>

@include('admin.user.partials.role_table')



</div>
</div>

</section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // /* ADD ROLE */
    const addBtn = document.getElementById('addRoleBtn');
    const roleInput = document.getElementById('new_role');
    const roleSelect = document.getElementById('roleSelect');
    const errorBox = document.getElementById('roleError');

    addBtn.addEventListener('click', function () {
        const roleName = roleInput.value.trim();

        if (!roleName) {
            errorBox.textContent = 'Role name required';
            errorBox.classList.remove('d-none');
            return;
        }

        fetch("{{ route('admin.roles.ajax.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name: roleName })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                roleSelect.add(new Option(data.role.name, data.role.id, true, true));
                roleInput.value = '';
                errorBox.classList.add('d-none');
            }
        });
    });

    /* MODULE MOVE */
    document.getElementById('moveRight').onclick = () => move('allModules','assignedModules');
    document.getElementById('moveLeft').onclick  = () => move('assignedModules','allModules');

    function move(from,to){
        const f=document.getElementById(from);
        const t=document.getElementById(to);
        [...f.selectedOptions].forEach(o=>t.appendChild(o));
    }

});
</script>
@endpush
