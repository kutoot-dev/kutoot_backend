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
            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-3" id="ledgerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="order-coins-tab" data-toggle="tab" href="#order-coins-pane" role="tab" aria-controls="order-coins-pane" aria-selected="true">
                        <i class="fas fa-coins"></i> Order Coins
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="campaign-coins-tab" data-toggle="tab" href="#campaign-coins-pane" role="tab" aria-controls="campaign-coins-pane" aria-selected="false">
                        <i class="fas fa-star"></i> Campaign Coins
                    </a>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content" id="ledgerTabsContent">
                <!-- Order Coins Tab -->
                <div class="tab-pane fade show active" id="order-coins-pane" role="tabpanel" aria-labelledby="order-coins-tab">
                    <form method="GET" class="form-row mb-3" id="orderLedgerFilterForm">
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
                            <input class="form-control" id="orderSearchInput" placeholder="Txn/Status" value="{{ request('search') }}">
                        </div>
                        <div class="form-group col-md-1 d-flex align-items-end">
                            <button class="btn btn-primary btn-block" type="button" id="orderApplyFilterBtn">Apply</button>
                        </div>
                        <div class="form-group col-md-1 d-flex align-items-end">
                            <button class="btn btn-outline-secondary btn-block" type="button" id="orderResetFilterBtn">Reset</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped" id="orderLedgerTable" style="width:100%">
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

                <!-- Campaign Coins Tab -->
                <div class="tab-pane fade" id="campaign-coins-pane" role="tabpanel" aria-labelledby="campaign-coins-tab">
                    <form method="GET" class="form-row mb-3" id="campaignLedgerFilterForm">
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

                        <input type="hidden" name="from_dt" value="">
                        <input type="hidden" name="to_dt" value="">

                        <div class="form-group col-md-2">
                            <label>Search</label>
                            <input class="form-control" id="campaignSearchInput" placeholder="Campaign/Status" value="">
                        </div>
                        <div class="form-group col-md-1 d-flex align-items-end">
                            <button class="btn btn-primary btn-block" type="button" id="campaignApplyFilterBtn">Apply</button>
                        </div>
                        <div class="form-group col-md-1 d-flex align-items-end">
                            <button class="btn btn-outline-secondary btn-block" type="button" id="campaignResetFilterBtn">Reset</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped" id="campaignLedgerTable" style="width:100%">
                            <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Campaign Name</th>
                                <th>Purchase Date</th>
                                <th>Coins Earned</th>
                                <th>Coins Used</th>
                                <th>Balance</th>
                                <th>Expires In</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
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

            // Initialize order coins tab
            const orderFromInit = splitDt($('input[name="from_dt"]').val());
            const orderToInit = splitDt($('input[name="to_dt"]').val());
            $('#orderLedgerFilterForm input[name="from_date"]').val(orderFromInit.date);
            $('#orderLedgerFilterForm input[name="from_time"]').val(orderFromInit.time);
            $('#orderLedgerFilterForm input[name="to_date"]').val(orderToInit.date);
            $('#orderLedgerFilterForm input[name="to_time"]').val(orderToInit.time);
            $('#orderLedgerFilterForm input[name="from_date"], #orderLedgerFilterForm input[name="from_time"], #orderLedgerFilterForm input[name="to_date"], #orderLedgerFilterForm input[name="to_time"]').on('change keyup', updateOrderHiddenDt);
            updateOrderHiddenDt();

            // Campaign coins tab has its own date handling
            $('#campaignLedgerFilterForm input[name="from_date"], #campaignLedgerFilterForm input[name="from_time"], #campaignLedgerFilterForm input[name="to_date"], #campaignLedgerFilterForm input[name="to_time"]').on('change keyup', updateCampaignHiddenDt);
            updateCampaignHiddenDt();
        });
    })(jQuery);

    function splitDt(dtStr) {
        dtStr = (dtStr || '').trim();
        if (!dtStr) return { date: '', time: '' };
        const m = window.moment ? moment(dtStr, ['DD-MM-YYYY HH:mm', 'YYYY-MM-DDTHH:mm', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DD', moment.ISO_8601], true) : null;
        if (m && m.isValid()) return { date: m.format('DD-MM-YYYY'), time: m.format('HH:mm') };
        const parts = dtStr.split(/[T ]/);
        return { date: parts[0] || '', time: (parts[1] || '').slice(0, 5) };
    }

    function updateOrderHiddenDt() {
        const fromDate = ($('#orderLedgerFilterForm input[name="from_date"]').val() || '').trim();
        const fromTime = ($('#orderLedgerFilterForm input[name="from_time"]').val() || '').trim();
        const toDate = ($('#orderLedgerFilterForm input[name="to_date"]').val() || '').trim();
        const toTime = ($('#orderLedgerFilterForm input[name="to_time"]').val() || '').trim();
        const fromDt = fromDate ? `${fromDate} ${(fromTime || '00:00')}` : '';
        const toDt = toDate ? `${toDate} ${(toTime || '23:59')}` : '';
        $('#orderLedgerFilterForm input[name="from_dt"]').val(fromDt);
        $('#orderLedgerFilterForm input[name="to_dt"]').val(toDt);
    }

    function updateCampaignHiddenDt() {
        const fromDate = ($('#campaignLedgerFilterForm input[name="from_date"]').val() || '').trim();
        const fromTime = ($('#campaignLedgerFilterForm input[name="from_time"]').val() || '').trim();
        const toDate = ($('#campaignLedgerFilterForm input[name="to_date"]').val() || '').trim();
        const toTime = ($('#campaignLedgerFilterForm input[name="to_time"]').val() || '').trim();
        const fromDt = fromDate ? `${fromDate} ${(fromTime || '00:00')}` : '';
        const toDt = toDate ? `${toDate} ${(toTime || '23:59')}` : '';
        $('#campaignLedgerFilterForm input[name="from_dt"]').val(fromDt);
        $('#campaignLedgerFilterForm input[name="to_dt"]').val(toDt);
    }

    // ============ ORDER COINS TABLE ============
    const orderTable = $('#orderLedgerTable').DataTable({
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
                updateOrderHiddenDt();
                d.from_dt = $('#orderLedgerFilterForm input[name="from_dt"]').val();
                d.to_dt = $('#orderLedgerFilterForm input[name="to_dt"]').val();
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

    $('#orderApplyFilterBtn').on('click', function () {
        const v = $('#orderSearchInput').val() || '';
        orderTable.search(v).draw();
    });

    $('#orderResetFilterBtn').on('click', function () {
        $('#orderLedgerFilterForm input[name="from_date"]').val('');
        $('#orderLedgerFilterForm input[name="from_time"]').val('');
        $('#orderLedgerFilterForm input[name="to_date"]').val('');
        $('#orderLedgerFilterForm input[name="to_time"]').val('');
        $('#orderLedgerFilterForm input[name="from_dt"]').val('');
        $('#orderLedgerFilterForm input[name="to_dt"]').val('');
        $('#orderSearchInput').val('');
        orderTable.search('').draw();
        orderTable.ajax.reload(null, true);
    });

    $('#orderSearchInput').on('keyup', function (e) {
        if (e.key === 'Enter') orderTable.search(this.value).draw();
    });

    // ============ CAMPAIGN COINS TABLE ============
    const campaignTable = $('#campaignLedgerTable').DataTable({
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
        order: [[2, 'desc']], // purchase_date (shifted by Sr No)
        ajax: {
            url: "{{ route('store.users.ledger.campaign-data', ['user' => $user->id]) }}",
            data: function (d) {
                updateCampaignHiddenDt();
                d.from_dt = $('#campaignLedgerFilterForm input[name="from_dt"]').val();
                d.to_dt = $('#campaignLedgerFilterForm input[name="to_dt"]').val();
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
            { data: 'camp_title', name: 'camp_title', responsivePriority: 1 },
            {
                data: 'purchase_date',
                name: 'purchase_date',
                responsivePriority: 3,
                render: function (data) {
                    if (!data) return '-';
                    const m = window.moment ? moment(data) : null;
                    const formatted = m ? m.format('DD-MM-YYYY hh:mm A') : data;
                    return '<span title=\"' + data + '\">' + formatted + '</span>';
                }
            },
            { data: 'coins_earned', name: 'coins_earned', responsivePriority: 2 },
            { data: 'coins_used', name: 'coins_used', responsivePriority: 4 },
            { data: 'coins_balance', name: 'coins_balance', responsivePriority: 5 },
            {
                data: 'expires_in',
                name: 'expires_in',
                responsivePriority: 6,
                render: function (data, type, row) {
                    if (row.is_expired) {
                        return '<span class=\"badge badge-danger\">Expired</span>';
                    }
                    return '<span class=\"badge badge-warning\">' + data + '</span>';
                }
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                responsivePriority: 1,
                render: function (data) {
                    if (data === 'completed' || data === 'success') return '<span class=\"badge badge-success\">Completed</span>';
                    if (data === 'pending') return '<span class=\"badge badge-warning\">Pending</span>';
                    if (data === 'failed') return '<span class=\"badge badge-danger\">Failed</span>';
                    return '<span class=\"badge badge-secondary\">' + data + '</span>';
                }
            },
        ],
    });

    $('#campaignApplyFilterBtn').on('click', function () {
        const v = $('#campaignSearchInput').val() || '';
        campaignTable.search(v).draw();
    });

    $('#campaignResetFilterBtn').on('click', function () {
        $('#campaignLedgerFilterForm input[name="from_date"]').val('');
        $('#campaignLedgerFilterForm input[name="from_time"]').val('');
        $('#campaignLedgerFilterForm input[name="to_date"]').val('');
        $('#campaignLedgerFilterForm input[name="to_time"]').val('');
        $('#campaignLedgerFilterForm input[name="from_dt"]').val('');
        $('#campaignLedgerFilterForm input[name="to_dt"]').val('');
        $('#campaignSearchInput').val('');
        campaignTable.search('').draw();
        campaignTable.ajax.reload(null, true);
    });

    $('#campaignSearchInput').on('keyup', function (e) {
        if (e.key === 'Enter') campaignTable.search(this.value).draw();
    });

    // Handle tab switching to reinitialize DataTables
    $('#order-coins-tab').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).css('width', '100%');
        orderTable.columns.adjust().draw(false);
    });

    $('#campaign-coins-tab').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).css('width', '100%');
        campaignTable.columns.adjust().draw(false);
    });
</script>
@endpush
@endsection


