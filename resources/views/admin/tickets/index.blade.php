@extends('admin.master_layout')
@section('title')
<title>{{__('Campaigns')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Campaigns')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.campaingns')}}</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="mb-4">Tickets for Campaign: {{ $campaign->name }}</h2>

    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary mb-3">← Back to Campaigns</a>

    {{-- Ticket Generation Form --}}
    <form method="POST" action="{{ route('admin.tickets.generate', $campaign->id) }}" class="mb-4" style="max-width: 400px;">
        @csrf
        <div class="input-group">
            <input type="number" name="quantity" class="form-control" placeholder="How many?" min="1" max="1000" required>
            <button class="btn btn-success">Generate Tickets</button>
        </div>
    </form>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                     {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
                      </div>
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                    <th>#</th>
                    <th>Ticket Code</th>
                    <th>Issued At</th>
                </tr>
                            </thead>
                            <tbody>
                           
                                  @forelse ($tickets as $ticket)
                    <tr>
                        <td>{{ $loop->iteration + (($tickets->currentPage() - 1) * $tickets->perPage()) }}</td>
                        <td><strong>{{ $ticket->ticket_code }}</strong></td>
                        <td>{{ $ticket->issued_at ? $ticket->issued_at->format('Y-m-d H:i') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No tickets yet for this campaign.</td></tr>
                @endforelse
                            </tbody>
                        </table>
                      </div>
                      {{-- Pagination --}}
    {{ $tickets->links() }}
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
      
@endsection
