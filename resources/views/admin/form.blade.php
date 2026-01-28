@extends('admin.master_layout')

@section('title')
<title>{{ isset($admin) ? 'Edit Admin' : 'Create Admin' }}</title>
@endsection

@section('admin-content')
<div class="main-content">
<section class="section">
<div class="section-header">
    <h1>{{ isset($admin) ? 'Edit Admin' : 'Create Admin' }}</h1>
</div>

<div class="section-body">
<div class="card">
<div class="card-body">

{{-- ✅ SUCCESS MESSAGE --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

{{-- ❌ VALIDATION ERRORS --}}
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ isset($admin) ? route('admin.update', $admin->id) : route('admin.store') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @if(isset($admin))
        @method('PUT')
    @endif

    <div class="row">

        <!-- Name -->
        <div class="form-group col-md-6">
            <label>Name *</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $admin->name ?? '') }}">
        </div>

        <!-- Email -->
        <div class="form-group col-md-6">
            <label>Email *</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email', $admin->email ?? '') }}">
        </div>

        <!-- Role -->
        <div class="form-group col-md-6">
            <label>Role</label>
            <select name="role_id" class="form-control">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        {{ old('role_id', $admin->role_id ?? '') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Image -->
        <div class="form-group col-md-6">
            <label>Profile Image</label>
            <input type="file" name="image" class="form-control">

            @if(isset($admin) && $admin->image)
                <img src="{{ asset($admin->image) }}" width="80" class="mt-2 rounded">
            @endif
        </div>

    </div>

    <button type="submit" class="btn btn-primary">
        {{ isset($admin) ? 'Update Admin' : 'Create Admin' }}
    </button>

</form>

</div>
</div>
</div>

</section>
</div>
@endsection
