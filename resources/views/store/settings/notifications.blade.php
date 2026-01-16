@extends('store.master_layout')

@section('store-content')
<section class="section">
    <div class="section-header">
        <h1>Notification Settings</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('store.settings.notifications.update') }}">
                @csrf

                <div class="form-group">
                    <label>Enabled</label>
                    <select class="form-control" name="enabled">
                        <option value="1" {{ $n->enabled ? 'selected' : '' }}>On</option>
                        <option value="0" {{ !$n->enabled ? 'selected' : '' }}>Off</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <select class="form-control" name="email">
                            <option value="1" {{ $n->email ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$n->email ? 'selected' : '' }}>Off</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>SMS</label>
                        <select class="form-control" name="sms">
                            <option value="1" {{ $n->sms ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$n->sms ? 'selected' : '' }}>Off</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>WhatsApp</label>
                        <select class="form-control" name="whatsapp">
                            <option value="1" {{ $n->whatsapp ? 'selected' : '' }}>On</option>
                            <option value="0" {{ !$n->whatsapp ? 'selected' : '' }}>Off</option>
                        </select>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection


