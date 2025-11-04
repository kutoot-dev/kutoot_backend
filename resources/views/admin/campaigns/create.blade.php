@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Create Coin Campaign')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Campaign')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.campaigns.index') }}">{{__('admin.campaigns')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Campaign')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                     <form method="POST" action="{{ route('admin.campaigns.store') }}">
        @csrf

       @php
    $editing = isset($campaign);
@endphp

<div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $campaign->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Series Prefix</label>
    <input type="text" name="series_prefix" class="form-control" maxlength="1" value="{{ old('series_prefix', $campaign->series_prefix ?? '') }}" required>
</div>

<div class="row mb-3">
    <div class="col">
        <label>Min Number</label>
        <input type="number" name="number_min" class="form-control" value="{{ old('number_min', $campaign->number_min ?? 1) }}" required>
    </div>
    <div class="col">
        <label>Max Number</label>
        <input type="number" name="number_max" class="form-control" value="{{ old('number_max', $campaign->number_max ?? 49) }}" required>
    </div>
</div>

<div class="mb-3">
    <label>Numbers Per Ticket</label>
    <select name="numbers_per_ticket" class="form-select" required>
        @foreach([2,3,4,5,6] as $n)
            <option value="{{ $n }}" {{ old('numbers_per_ticket', $campaign->numbers_per_ticket ?? 6) == $n ? 'selected' : '' }}>
                {{ $n }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Goal Target (optional)</label>
    <input type="number" name="goal_target" class="form-control" value="{{ old('goal_target', $campaign->goal_target ?? '') }}">
</div>



        <button type="submit" class="btn btn-success">Create Campaign</button>
    </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

@endsection
