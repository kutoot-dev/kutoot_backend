@extends('store.master_layout')

@section('store-content')
<section class="section">
    <div class="section-header">
        <h1>Change Password</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('store.settings.change-password.submit') }}">
                @csrf

                <div class="form-group">
                    <label>Old Password</label>
                    <input type="password" class="form-control" name="old_password" required>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" class="form-control" name="new_password_confirmation" required>
                </div>

                <button class="btn btn-primary" type="submit">Update Password</button>
            </form>
        </div>
    </div>
</section>
@endsection


