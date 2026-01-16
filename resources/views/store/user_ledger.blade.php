@extends('store.master_layout')

@section('store-content')
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<style>
    .dt-buttons .btn { margin-right: .35rem; }
    table.dataTable tbody td { vertical-align: middle; }

    /* Responsive Filter Form */
    @media (max-width: 991.98px) {
        #ledgerFilterForm .form-group {
            margin-bottom: 0.75rem;
        }
        #ledgerFilterForm .form-group.col-md-1 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        #ledgerFilterForm .form-group.col-md-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    @media (max-width: 575.98px) {
        #ledgerFilterForm .form-group.col-md-1,
        #ledgerFilterForm .form-group.col-md-2 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .dt-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }
        .dt-buttons .btn {
            margin-right: 0 !important;
            padding: 0.375rem 0.5rem !important;
            font-size: 0.75rem !important;
        }
    }
</style>
@endpush

@php
    $phone = (string) ($user?->phone ?? '');
    $maskedPhone = $phone !== '' ? (substr($phone, 0, 3) . str_repeat('X', max(0, strlen($phone) - 3))) : null;
    $currency = config('kutoot.currency_symbol', '₹');
@endphp

<section class="section">
    <div class="section-header d-flex align-items-center justify-content-between">
        <h1>User Ledger</h1>
        <div>
            <a href="{{ route('store.visitors') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Visitors
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">User</div>
                    <div class="h6 mb-0">{{ $user->name ?? '-' }}</div>
                    <div class="text-muted">{{ $maskedPhone ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Current Coin Balance</div>
                    <div class="h4 mb-0">{{ (int) $balanceCoins }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Total Coins Redeemed (This Store)</div>
                    <div class="h4 mb-0">{{ (int) ($totals->total_coins ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Discount Given (This Store)</div>
                    <div class="h4 mb-0">{{ $currency }} {{ number_format((float) ($totals->total_discount ?? 0), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="form-row" id="ledgerFilterForm">
                <div class="form-group col-md-2">
                    <label>From (Date)</label>
                    <input type="text" class="form-control datepicker-india" name="from_date" autocomplete="off" placeholder="DD-MM-YYYY">
                </div>
                <div class="form-group col-md-2">
                    <label>From (Time)</label>
                    <input type="text" class="form-control clockpicker" name="from_time" data-align="top" data-autoclose="true" autocomplete="off" placeholder="HH:MM">
                </div>
                <div class="form-group col-md-2">
                    <label>To (Date)</label>
                    <input type="text" class="form-control datepicker-india" name="to_date" autocomplete="off" placeholder="DD-MM-YYYY">
                </div>
                <div class="form-group col-md-2">
                    <label>To (Time)</label>
                    <input type="text" class="form-control clockpicker" name="to_time" data-align="top" data-autoclose="true" autocomplete="off" placeholder="HH:MM">
                </div>

                <input type="hidden" name="from_dt" value="{{ request('from_dt') }}">
                <input type="hidden" name="to_dt" value="{{ request('to_dt') }}">

                <div class="form-group col-md-2">
                    <label>Search</label>
                    <input class="form-control" id="searchInput" placeholder="Txn/Status" value="{{ request('search') }}">
                </div>
                <div class="form-group col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary btn-block" type="button" id="applyFilterBtn">Apply</button>
                </div>
                <div class="form-group col-md-1 d-flex align-items-end">
                    <button class="btn btn-outline-secondary btn-block" type="button" id="resetFilterBtn">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="ledgerTable" style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Txn ID</th>
                        <th>Visited</th>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>Redeemed Coins</th>
                        <th>Coins Debited (Wallet)</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    const CURRENCY = @json(config('kutoot.currency_symbol', '₹'));
    function splitDt(dtStr) {
        dtStr = (dtStr || '').trim();
        if (!dtStr) return { date: '', time: '' };
        const m = window.moment ? moment(dtStr, ['DD-MM-YYYY HH:mm', 'YYYY-MM-DDTHH:mm', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DD', moment.ISO_8601], true) : null;
        if (m && m.isValid()) return { date: m.format('DD-MM-YYYY'), time: m.format('HH:mm') };
        const parts = dtStr.split(/[T ]/);
        return { date: parts[0] || '', time: (parts[1] || '').slice(0, 5) };
    }

    function updateHiddenDt() {
        const fromDate = ($('input[name="from_date"]').val() || '').trim();
        const fromTime = ($('input[name="from_time"]').val() || '').trim();
        const toDate = ($('input[name="to_date"]').val() || '').trim();
        const toTime = ($('input[name="to_time"]').val() || '').trim();
        const fromDt = fromDate ? `${fromDate} ${(fromTime || '00:00')}` : '';
        const toDt = toDate ? `${toDate} ${(toTime || '23:59')}` : '';
        $('input[name="from_dt"]').val(fromDt);
        $('input[name="to_dt"]').val(toDt);
    }

    (function ($) {
        $(function () {
            $('.datepicker-india').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
                container: 'body',
                zIndexOffset: 100000,
                orientation: 'bottom auto'
            });
            $('.clockpicker').clockpicker();
            const fromInit = splitDt($('input[name="from_dt"]').val());
            const toInit = splitDt($('input[name="to_dt"]').val());
            $('input[name="from_date"]').val(fromInit.date);
            $('input[name="from_time"]').val(fromInit.time);
            $('input[name="to_date"]').val(toInit.date);
            $('input[name="to_time"]').val(toInit.time);
            $('input[name="from_date"], input[name="from_time"], input[name="to_date"], input[name="to_time"]').on('change keyup', updateHiddenDt);
            updateHiddenDt();
        });
    })(jQuery);

    const table = $('#ledgerTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        responsive: {
            details: {
                type: 'inline',
                target: 'td:first-child, th:first-child'
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        pageLength: 10,
        order: [[2, 'desc']], // visited_at (shifted by Sr No)
        ajax: {
            url: "{{ route('store.users.ledger.data', ['user' => $user->id]) }}",
            data: function (d) {
                updateHiddenDt();
                d.from_dt = $('input[name="from_dt"]').val();
                d.to_dt = $('input[name="to_dt"]').val();
            }
        },
        dom: "<'row align-items-center justify-content-between'<'col-md-6'<'d-flex align-items-center'B>><'col-md-6 text-right'l>>" +
             "<'row'<'col-md-12'tr>>" +
             "<'row'<'col-md-6'i><'col-md-6 text-right'p>>",
        buttons: [
            { extend: 'copyHtml5', text: '<i class=\"fas fa-copy\"></i>', titleAttr: 'Copy', className: 'btn btn-sm btn-primary' },
            { extend: 'csvHtml5', text: '<i class=\"fas fa-file-csv\"></i>', titleAttr: 'CSV', className: 'btn btn-sm btn-info' },
            { extend: 'excelHtml5', text: '<i class=\"fas fa-file-excel\"></i>', titleAttr: 'Excel', className: 'btn btn-sm btn-success' },
            { extend: 'pdfHtml5', text: '<i class=\"fas fa-file-pdf\"></i>', titleAttr: 'PDF', className: 'btn btn-sm btn-danger' },
            { extend: 'print', text: '<i class=\"fas fa-print\"></i>', titleAttr: 'Print', className: 'btn btn-sm btn-secondary' },
            { extend: 'colvis', text: '<i class=\"fas fa-columns\"></i>', titleAttr: 'Columns', className: 'btn btn-sm btn-dark' },
        ],
        columns: [
            { data: 'sr_no', name: 'sr_no', orderable: false, searchable: false, responsivePriority: 10 },
            { data: 'txn_code', name: 'txn_code', responsivePriority: 1 }, // Always visible
            {
                data: 'visited_at',
                name: 'visited_at',
                responsivePriority: 3,
                render: function (data) {
                    if (!data) return '-';
                    const m = window.moment ? moment(data) : null;
                    const formatted = m ? m.format('DD-MM-YYYY hh:mm A') : data;
                    return '<span title=\"' + data + '\">' + formatted + '</span>';
                }
            },
            { data: 'total_amount', name: 'total_amount', responsivePriority: 2, render: $.fn.dataTable.render.number(',', '.', 2, CURRENCY + ' ') },
            { data: 'discount_amount', name: 'discount_amount', responsivePriority: 5, render: $.fn.dataTable.render.number(',', '.', 2, CURRENCY + ' ') },
            { data: 'redeemed_coins', name: 'redeemed_coins', responsivePriority: 6 },
            { data: 'coins_debited', name: 'coins_debited', responsivePriority: 7 },
            {
                data: 'status',
                name: 'status',
                responsivePriority: 1, // Always visible
                render: function (data) {
                    if (data === 'SUCCESS') return '<span class=\"badge badge-success\">SUCCESS</span>';
                    if (data === 'FAILED') return '<span class=\"badge badge-danger\">FAILED</span>';
                    return '<span class=\"badge badge-secondary\">-</span>';
                }
            },
        ],
    });

    $('#applyFilterBtn').on('click', function () {
        const v = $('#searchInput').val() || '';
        table.search(v).draw();
    });

    $('#resetFilterBtn').on('click', function () {
        $('input[name="from_date"]').val('');
        $('input[name="from_time"]').val('');
        $('input[name="to_date"]').val('');
        $('input[name="to_time"]').val('');
        $('input[name="from_dt"]').val('');
        $('input[name="to_dt"]').val('');
        $('#searchInput').val('');
        table.search('').draw();
        table.ajax.reload(null, true);
    });

    $('#searchInput').on('keyup', function (e) {
        if (e.key === 'Enter') table.search(this.value).draw();
    });
</script>
@endpush
@endsection


