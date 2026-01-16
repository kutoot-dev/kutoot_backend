@extends('store.master_layout')

@section('store-content')
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<style>
    /* Make DataTables match the backend theme */
    div.dataTables_wrapper div.dataTables_length select {
        min-width: 70px;
    }
    .dt-buttons .btn {
        margin-right: .35rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin: 0 .15rem !important;
    }
    table.dataTable tbody td {
        vertical-align: middle;
    }

    /* Responsive Filter Form */
    @media (max-width: 991.98px) {
        #visitorsFilterForm .form-group {
            margin-bottom: 0.75rem;
        }
        #visitorsFilterForm .form-group.col-md-1 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        #visitorsFilterForm .form-group.col-md-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    @media (max-width: 575.98px) {
        #visitorsFilterForm .form-group.col-md-1,
        #visitorsFilterForm .form-group.col-md-2 {
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

    /* Responsive DataTable */
    .dataTables_wrapper .row {
        margin-left: 0;
        margin-right: 0;
    }
    @media (max-width: 767.98px) {
        .dataTables_wrapper .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: left !important;
            margin-bottom: 0.5rem;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
        }
    }

    /* Responsive expand/collapse icon - handled by store-shadcn.css */
    table.dataTable > tbody > tr.child ul.dtr-details {
        display: block;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    table.dataTable > tbody > tr.child ul.dtr-details > li {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--sh-border, #e4e4e7);
    }
    table.dataTable > tbody > tr.child ul.dtr-details > li:last-child {
        border-bottom: none;
    }
    table.dataTable > tbody > tr.child span.dtr-title {
        font-weight: 600;
        color: var(--sh-muted-foreground, #71717a);
        min-width: 100px;
    }
    table.dataTable > tbody > tr.child span.dtr-data {
        text-align: right;
    }
</style>
@endpush

<section class="section">
    <div class="section-header">
        <h1>Visitors</h1>
        <div class="section-header-button">
            <button class="btn btn-primary" id="addVisitorBtn">
                <i class="fas fa-plus"></i> Add Transaction
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="form-row" id="visitorsFilterForm">
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

                {{-- Hidden combined values used by server-side DataTable --}}
                <input type="hidden" name="from_dt" value="{{ request('from_dt') }}">
                <input type="hidden" name="to_dt" value="{{ request('to_dt') }}">


                <div class="form-group col-md-2">
                    <label>Search (User)</label>
                    <input class="form-control" id="searchInput" placeholder="Name/Phone/ID/Txn" value="{{ request('search') }}">
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
                <table class="table table-striped" id="visitorsTable" style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Visitor ID</th>
                        <th>User Name</th>
                        <th>User Phone</th>
                        <th>Visited</th>
                        <th>Redeemed</th>
                        <th>Txn ID</th>
                        <th>Amount</th>
                        <th>Discount Applied</th>
                        <th>Redeemed Coins</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTransactionModalLabel">Add Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    <input type="hidden" id="transactionVisitorId" name="visitor_id">
                    
                    <div class="form-group">
                        <label>Visitor Information</label>
                        <div class="alert alert-info">
                            <strong>Name:</strong> <span id="visitorName">-</span><br>
                            <strong>Phone:</strong> <span id="visitorPhone">-</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="billAmount">Bill Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ config('kutoot.currency_symbol', '₹') }}</span>
                            </div>
                            <input type="number" class="form-control" id="billAmount" name="bill_amount" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="txnCode">Transaction ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txnCode" name="txn_code" placeholder="e.g., TXN-12345" required>
                        <small class="form-text text-muted">Must be unique across all transactions.</small>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-info" id="calculateBtn">
                            <i class="fas fa-calculator"></i> Calculate
                        </button>
                    </div>

                    <div id="calculationResults" style="display: none;">
                        <hr>
                        <h6>Calculation Results</h6>
                        <div class="alert alert-warning" id="eligibilityAlert"></div>
                        
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th>Available Coins:</th>
                                <td id="availableCoins">-</td>
                            </tr>
                            <tr>
                                <th>Minimum Bill Amount:</th>
                                <td>{{ config('kutoot.currency_symbol', '₹') }}<span id="minimumBillAmount">-</span></td>
                            </tr>
                            <tr>
                                <th>Max Discount (%):</th>
                                <td><span id="discountPercent">-</span>%</td>
                            </tr>
                            <tr>
                                <th>Max Discount Amount:</th>
                                <td>{{ config('kutoot.currency_symbol', '₹') }}<span id="maxDiscountAmount">-</span></td>
                            </tr>
                            <tr>
                                <th>Redeemable Coins:</th>
                                <td><span id="redeemableCoins">-</span></td>
                            </tr>
                            <tr>
                                <th>Discount Amount:</th>
                                <td><strong>{{ config('kutoot.currency_symbol', '₹') }}<span id="discountAmount">-</span></strong></td>
                            </tr>
                            <tr>
                                <th>Final Amount:</th>
                                <td><strong>{{ config('kutoot.currency_symbol', '₹') }}<span id="finalAmount">-</span></strong></td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="createTransactionBtn" disabled>
                    <i class="fas fa-save"></i> Create Transaction
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Visitor Modal -->
<div class="modal fade" id="addVisitorModal" tabindex="-1" role="dialog" aria-labelledby="addVisitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVisitorModalLabel">Add Transaction with User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addVisitorForm">
                    <div class="form-group">
                        <label for="userSearch">Search User <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="userSearch" name="user_search" placeholder="Search by name, phone, or email..." autocomplete="off">
                        <small class="form-text text-muted">Type at least 2 characters to search</small>
                        <div id="userSearchResults" class="mt-2" style="display: none;">
                            <div class="list-group" id="userResultsList"></div>
                        </div>
                    </div>

                    <input type="hidden" id="selectedUserId" name="user_id">
                    
                    <div id="selectedUserInfo" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Selected User:</strong>
                            <div id="selectedUserName"></div>
                            <div id="selectedUserPhone"></div>
                            <div id="selectedUserEmail"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="billAmountModal">Bill Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ config('kutoot.currency_symbol', '₹') }}</span>
                            </div>
                            <input type="number" class="form-control" id="billAmountModal" name="bill_amount" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-info" id="calculateBtnModal">
                            <i class="fas fa-calculator"></i> Calculate
                        </button>
                    </div>

                    <div id="calculationResultsModal" style="display: none;">
                        <hr>
                        <h6>Calculation Results</h6>
                        <div class="alert alert-warning" id="eligibilityAlertModal"></div>
                        
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th>Available Coins:</th>
                                <td id="availableCoinsModal">-</td>
                            </tr>
                            <tr>
                                <th>Minimum Bill Amount:</th>
                                <td>{{ config('kutoot.currency_symbol', '₹') }}<span id="minimumBillAmountModal">-</span></td>
                            </tr>
                            <tr>
                                <th>Max Discount (%):</th>
                                <td><span id="discountPercentModal">-</span>%</td>
                            </tr>
                            <tr>
                                <th>Max Discount Amount:</th>
                                <td>{{ config('kutoot.currency_symbol', '₹') }}<span id="maxDiscountAmountModal">-</span></td>
                            </tr>
                            <tr>
                                <th>Redeemable Coins:</th>
                                <td><span id="redeemableCoinsModal">-</span></td>
                            </tr>
                            <tr>
                                <th>Discount Amount:</th>
                                <td><strong>{{ config('kutoot.currency_symbol', '₹') }}<span id="discountAmountModal">-</span></strong></td>
                            </tr>
                            <tr>
                                <th>Final Amount:</th>
                                <td><strong>{{ config('kutoot.currency_symbol', '₹') }}<span id="finalAmountModal">-</span></strong></td>
                            </tr>
                        </table>
                    </div>

                    <div class="form-group">
                        <label for="txnCodeModal">Transaction ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txnCodeModal" name="txn_code" placeholder="e.g., TXN-12345" required>
                        <small class="form-text text-muted">Must be unique across all transactions.</small>
                    </div>

                    <div class="form-group">
                        <label for="visitedOn">Visited Date</label>
                        <input type="date" class="form-control" id="visitedOn" name="visited_on" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="redeemed" name="redeemed" value="1">
                            <label class="form-check-label" for="redeemed">
                                Redeemed
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveVisitorBtn" disabled>
                    <i class="fas fa-save"></i> Add Transaction
                </button>
            </div>
        </div>
    </div>
</div>

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

        // Accept either "dd-mm-yyyy HH:MM" or ISO "yyyy-mm-ddTHH:MM"
        const m = window.moment ? moment(dtStr, ['DD-MM-YYYY HH:mm', 'YYYY-MM-DDTHH:mm', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DD', moment.ISO_8601], true) : null;
        if (m && m.isValid()) {
            return { date: m.format('DD-MM-YYYY'), time: m.format('HH:mm') };
        }

        // best-effort fallback
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

            // Hydrate visible inputs from query-string (hidden inputs)
            const fromInit = splitDt($('input[name="from_dt"]').val());
            const toInit = splitDt($('input[name="to_dt"]').val());
            $('input[name="from_date"]').val(fromInit.date);
            $('input[name="from_time"]').val(fromInit.time);
            $('input[name="to_date"]').val(toInit.date);
            $('input[name="to_time"]').val(toInit.time);

            // Keep hidden inputs in sync
            $('input[name="from_date"], input[name="from_time"], input[name="to_date"], input[name="to_time"]').on('change keyup', updateHiddenDt);
            updateHiddenDt();
        });
    })(jQuery);

    const table = $('#visitorsTable').DataTable({
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
        order: [[4, 'desc']], // visited_at (shifted by Sr No)
        ajax: {
            url: "{{ route('store.visitors.data') }}",
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
            { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i>', titleAttr: 'Copy', className: 'btn btn-sm btn-primary' },
            { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i>', titleAttr: 'CSV', className: 'btn btn-sm btn-info' },
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i>', titleAttr: 'Excel', className: 'btn btn-sm btn-success' },
            { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i>', titleAttr: 'PDF', className: 'btn btn-sm btn-danger' },
            { extend: 'print', text: '<i class="fas fa-print"></i>', titleAttr: 'Print', className: 'btn btn-sm btn-secondary' },
            { extend: 'colvis', text: '<i class="fas fa-columns"></i>', titleAttr: 'Columns', className: 'btn btn-sm btn-dark' },
        ],
        columns: [
            { data: 'sr_no', name: 'sr_no', orderable: false, searchable: false, responsivePriority: 10 },
            { data: 'visitor_id', name: 'visitor_id', responsivePriority: 8 },
            { data: 'name', name: 'name', responsivePriority: 1 }, // Always visible
            { data: 'phone', name: 'phone', responsivePriority: 3 },
            {
                data: 'visited_at',
                name: 'visited_at',
                responsivePriority: 4,
                render: function (data) {
                    if (!data) return '-';
                    const m = window.moment ? moment(data) : null;
                    const formatted = m ? m.format('DD-MM-YYYY hh:mm A') : data;
                    return '<span title="' + data + '">' + formatted + '</span>';
                }
            },
            {
                data: 'redeemed',
                name: 'redeemed',
                responsivePriority: 7,
                render: function (data) {
                    return data ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                }
            },
            { data: 'txn_code', name: 'txn_code', defaultContent: '-', responsivePriority: 9 },
            {
                data: 'total_amount',
                name: 'total_amount',
                responsivePriority: 2, // Important - always visible
                render: $.fn.dataTable.render.number(',', '.', 2, CURRENCY + ' ')
            },
            {
                data: 'discount_amount',
                name: 'discount_amount',
                responsivePriority: 6,
                render: $.fn.dataTable.render.number(',', '.', 2, CURRENCY + ' ')
            },
            { data: 'redeemed_coins', name: 'redeemed_coins', responsivePriority: 7 },
            {
                data: 'status',
                name: 'status',
                responsivePriority: 2, // Important - always visible
                render: function (data) {
                    if (data === 'SUCCESS') return '<span class="badge badge-success">SUCCESS</span>';
                    if (data === 'FAILED') return '<span class="badge badge-danger">FAILED</span>';
                    return '<span class="badge badge-secondary">-</span>';
                }
            },
            {
                data: 'user_id',
                name: 'user_id',
                orderable: false,
                searchable: false,
                responsivePriority: 1, // Always visible
                render: function (data, type, row) {
                    // Only render HTML for display, not for sorting/filtering
                    if (type !== 'display') {
                        return data || '';
                    }
                    
                    let html = '';
                    if (data) {
                        const base = "{{ url('store/users') }}";
                        const href = base + '/' + data + '/ledger';
                        html += '<a href="' + href + '" class="btn btn-sm btn-dark" target="_blank" title="Ledger"><i class="fas fa-receipt"></i></a> ';
                    }
                    
                    // Show button if user_id exists (allow multiple transactions per visitor)
                    if (row.user_id) {
                        html += '<button class="btn btn-sm btn-primary add-transaction-btn" data-visitor-id="' + row.visitor_id + '" data-user-id="' + row.user_id + '" title="Add Transaction"><i class="fas fa-plus"></i> Add</button>';
                    }
                    
                    return html || '-';
                }
            }
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
        if (e.key === 'Enter') {
            table.search(this.value).draw();
        }
    });

    // Add Transaction Modal
    let currentVisitorId = null;
    let calculationData = null;
    let calculateTimeout = null;
    let isCalculating = false;

    $(document).on('click', '.add-transaction-btn', function() {
        const visitorId = $(this).data('visitor-id');
        const userId = $(this).data('user-id');
        currentVisitorId = visitorId;
        
        // Get visitor info from table row
        const row = table.row($(this).closest('tr')).data();
        $('#visitorName').text(row.name || '-');
        $('#visitorPhone').text(row.phone || '-');
        $('#transactionVisitorId').val(visitorId);
        $('#billAmount').val('');
        $('#txnCode').val('');
        $('#calculationResults').hide();
        $('#createTransactionBtn').prop('disabled', true);
        $('#addTransactionModal').modal('show');
        
        // Focus on bill amount input
        setTimeout(function() {
            $('#billAmount').focus();
        }, 500);
    });

    // Auto-calculate on bill amount change (with debouncing)
    $('#billAmount').on('input change', function() {
        const billAmount = parseFloat($(this).val());
        
        // Clear previous timeout
        if (calculateTimeout) {
            clearTimeout(calculateTimeout);
        }
        
        // Hide results if amount is invalid
        if (!billAmount || billAmount <= 0) {
            $('#calculationResults').hide();
            $('#createTransactionBtn').prop('disabled', true);
            return;
        }
        
        // Show loading indicator
        if (!isCalculating) {
            $('#calculationResults').hide();
            $('#calculateBtn').html('<i class="fas fa-spinner fa-spin"></i> Calculating...');
        }
        
        // Debounce: wait 500ms after user stops typing
        calculateTimeout = setTimeout(function() {
            performCalculation(billAmount);
        }, 500);
    });

    // Manual calculate button (optional, can be hidden)
    $('#calculateBtn').on('click', function() {
        const billAmount = parseFloat($('#billAmount').val());
        if (!billAmount || billAmount <= 0) {
            alert('Please enter a valid bill amount');
            return;
        }
        performCalculation(billAmount);
    });

    function performCalculation(billAmount) {
        if (!currentVisitorId) {
            alert('Visitor ID not found');
            return;
        }

        if (isCalculating) {
            return; // Prevent multiple simultaneous requests
        }

        isCalculating = true;
        $('#calculateBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Calculating...');

        $.ajax({
            url: "{{ url('store/visitors') }}/" + currentVisitorId + "/calculate-transaction",
            method: 'GET',
            data: { bill_amount: billAmount },
            success: function(response) {
                if (response.success) {
                    calculationData = response.data;
                    displayCalculation(response.data);
                    $('#createTransactionBtn').prop('disabled', false);
                } else {
                    $('#calculationResults').hide();
                    alert(response.message || 'Calculation failed');
                }
            },
            error: function(xhr) {
                $('#calculationResults').hide();
                const error = xhr.responseJSON?.message || 'Failed to calculate';
                // Don't show alert on every error, just log it
                console.error('Calculation error:', error);
            },
            complete: function() {
                isCalculating = false;
                $('#calculateBtn').prop('disabled', false).html('<i class="fas fa-calculator"></i> Calculate');
            }
        });
    }

    function displayCalculation(data) {
        const calc = data.calculation;
        const settings = data.settings;

        $('#availableCoins').text(calc.available_coins);
        $('#minimumBillAmount').text(settings.minimum_bill_amount.toFixed(2));
        $('#discountPercent').text(settings.discount_percent.toFixed(2));
        $('#maxDiscountAmount').text(calc.max_discount_by_percent.toFixed(2));
        $('#redeemableCoins').text(calc.redeemable_coins);
        $('#discountAmount').text(calc.discount_amount.toFixed(2));
        $('#finalAmount').text(calc.final_amount.toFixed(2));

        // Eligibility alert
        if (calc.is_eligible) {
            $('#eligibilityAlert').removeClass('alert-warning alert-danger').addClass('alert-success')
                .html('<i class="fas fa-check-circle"></i> ' + calc.eligibility_message);
        } else {
            $('#eligibilityAlert').removeClass('alert-success alert-warning').addClass('alert-danger')
                .html('<i class="fas fa-exclamation-circle"></i> ' + calc.eligibility_message);
        }

        $('#calculationResults').show();
    }

    $('#createTransactionBtn').on('click', function() {
        if (!currentVisitorId) {
            alert('Visitor ID not found');
            return;
        }

        const billAmount = parseFloat($('#billAmount').val());
        if (!billAmount || billAmount <= 0) {
            alert('Please enter a valid bill amount');
            return;
        }

        if (!confirm('Are you sure you want to create this transaction?')) {
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

        const txnCode = $('#txnCode').val()?.trim();
        
        if (!txnCode) {
            alert('Please enter a Transaction ID');
            return;
        }

        $.ajax({
            url: "{{ url('store/visitors') }}/" + currentVisitorId + "/create-transaction",
            method: 'POST',
            data: {
                bill_amount: parseFloat(billAmount),
                txn_code: txnCode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Transaction created successfully!');
                    $('#addTransactionModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    alert(response.message || 'Failed to create transaction');
                }
            },
            error: function(xhr) {
                let error = 'Failed to create transaction';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        // Show validation errors
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        error = errors.join('\n');
                    } else if (xhr.responseJSON.message) {
                        error = xhr.responseJSON.message;
                    }
                }
                alert(error);
            },
            complete: function() {
                $('#createTransactionBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Create Transaction');
            }
        });
    });

    // Reset modal on close
    $('#addTransactionModal').on('hidden.bs.modal', function() {
        currentVisitorId = null;
        calculationData = null;
        $('#transactionForm')[0].reset();
        $('#calculationResults').hide();
        $('#createTransactionBtn').prop('disabled', true);
    });

    // Add Visitor Modal
    let userSearchTimeout = null;
    let selectedUser = null;

    $('#addVisitorBtn').on('click', function() {
        $('#addVisitorModal').modal('show');
        $('#userSearch').val('');
        $('#selectedUserId').val('');
        $('#selectedUserInfo').hide();
        $('#userSearchResults').hide();
        $('#saveVisitorBtn').prop('disabled', true);
        selectedUser = null;
        
        setTimeout(function() {
            $('#userSearch').focus();
        }, 500);
    });

    // User search with debouncing
    $('#userSearch').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        // Clear previous timeout
        if (userSearchTimeout) {
            clearTimeout(userSearchTimeout);
        }
        
        // Hide results if search is too short
        if (searchTerm.length < 2) {
            $('#userSearchResults').hide();
            $('#selectedUserInfo').hide();
            $('#selectedUserId').val('');
            $('#saveVisitorBtn').prop('disabled', true);
            selectedUser = null;
            return;
        }
        
        // Debounce: wait 500ms after user stops typing
        userSearchTimeout = setTimeout(function() {
            searchUsers(searchTerm);
        }, 500);
    });

    function searchUsers(searchTerm) {
        $.ajax({
            url: "{{ route('store.visitors.search-users') }}",
            method: 'GET',
            data: { search: searchTerm },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    displayUserResults(response.data);
                } else {
                    $('#userResultsList').html('<div class="list-group-item">No users found</div>');
                    $('#userSearchResults').show();
                }
            },
            error: function(xhr) {
                console.error('Search error:', xhr);
                $('#userResultsList').html('<div class="list-group-item text-danger">Error searching users</div>');
                $('#userSearchResults').show();
            }
        });
    }

    // Mask phone number (show first 3 digits, mask rest)
    function maskPhone(phone) {
        if (!phone || phone === 'N/A') return 'N/A';
        const phoneStr = String(phone);
        const len = phoneStr.length;
        if (len <= 3) return phoneStr;
        return phoneStr.substring(0, 3) + 'X'.repeat(len - 3);
    }

    // Mask email (show first 2 chars before @, mask rest)
    function maskEmail(email) {
        if (!email || email === 'N/A' || !email.includes('@')) return 'N/A';
        const parts = email.split('@');
        const username = parts[0];
        const domain = parts[1];
        if (username.length <= 2) {
            return username + '@' + domain;
        }
        const maskedUsername = username.substring(0, 2) + 'X'.repeat(Math.max(1, username.length - 2));
        return maskedUsername + '@' + domain;
    }

    function displayUserResults(users) {
        let html = '';
        users.forEach(function(user) {
            const maskedPhone = maskPhone(user.phone);
            const maskedEmail = maskEmail(user.email);
            html += '<a href="#" class="list-group-item list-group-item-action user-result-item" data-user-id="' + user.id + '" data-user-name="' + (user.name || 'N/A') + '" data-user-phone="' + (user.phone || 'N/A') + '" data-user-email="' + (user.email || 'N/A') + '">';
            html += '<div><strong>' + (user.name || 'N/A') + '</strong></div>';
            html += '<div class="text-muted small">Phone: ' + maskedPhone + ' | Email: ' + maskedEmail + '</div>';
            html += '</a>';
        });
        $('#userResultsList').html(html);
        $('#userSearchResults').show();
    }

    // Handle user selection
    $(document).on('click', '.user-result-item', function(e) {
        e.preventDefault();
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const userPhone = $(this).data('user-phone');
        const userEmail = $(this).data('user-email');
        
        selectedUser = {
            id: userId,
            name: userName,
            phone: userPhone,
            email: userEmail
        };
        
        $('#selectedUserId').val(userId);
        $('#selectedUserName').html('<strong>Name:</strong> ' + userName);
        $('#selectedUserPhone').html('<strong>Phone:</strong> ' + maskPhone(userPhone));
        $('#selectedUserEmail').html('<strong>Email:</strong> ' + maskEmail(userEmail));
        $('#selectedUserInfo').show();
        $('#userSearchResults').hide();
        
        // Auto-calculate if bill amount is already entered
        const billAmount = parseFloat($('#billAmountModal').val());
        if (billAmount && billAmount > 0) {
            performCalculationModal(billAmount);
        }
        
        // Check if all required fields are filled before enabling save button
        const txnCode = $('#txnCodeModal').val()?.trim();
        if (userId && billAmount && txnCode) {
            $('#saveVisitorBtn').prop('disabled', false);
        }
    });

    // Auto-calculate on bill amount change (with debouncing) for modal
    let calculateTimeoutModal = null;
    let isCalculatingModal = false;

    $('#billAmountModal').on('input change', function() {
        const billAmount = parseFloat($(this).val());
        const userId = $('#selectedUserId').val();
        
        // Clear previous timeout
        if (calculateTimeoutModal) {
            clearTimeout(calculateTimeoutModal);
        }
        
        // Hide results if amount is invalid or user not selected
        if (!billAmount || billAmount <= 0 || !userId) {
            $('#calculationResultsModal').hide();
            return;
        }
        
        // Show loading indicator
        if (!isCalculatingModal) {
            $('#calculationResultsModal').hide();
            $('#calculateBtnModal').html('<i class="fas fa-spinner fa-spin"></i> Calculating...');
        }
        
        // Debounce: wait 500ms after user stops typing
        calculateTimeoutModal = setTimeout(function() {
            performCalculationModal(billAmount);
        }, 500);
    });

    // Manual calculate button for modal
    $('#calculateBtnModal').on('click', function() {
        const billAmount = parseFloat($('#billAmountModal').val());
        if (!billAmount || billAmount <= 0) {
            alert('Please enter a valid bill amount');
            return;
        }
        if (!selectedUser) {
            alert('Please select a user first');
            return;
        }
        performCalculationModal(billAmount);
    });

    function performCalculationModal(billAmount) {
        const userId = $('#selectedUserId').val();
        if (!userId) {
            alert('Please select a user first');
            return;
        }

        if (isCalculatingModal) {
            return; // Prevent multiple simultaneous requests
        }

        isCalculatingModal = true;
        $('#calculateBtnModal').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Calculating...');

        const redeemed = $('#redeemed').is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('store.visitors.calculate-by-user') }}",
            method: 'GET',
            data: { 
                user_id: userId,
                bill_amount: billAmount,
                redeemed: redeemed
            },
            success: function(response) {
                if (response.success) {
                    displayCalculationModal(response.data);
                    $('#saveVisitorBtn').prop('disabled', false);
                } else {
                    $('#calculationResultsModal').hide();
                    alert(response.message || 'Calculation failed');
                }
            },
            error: function(xhr) {
                $('#calculationResultsModal').hide();
                const error = xhr.responseJSON?.message || 'Failed to calculate';
                console.error('Calculation error:', error);
            },
            complete: function() {
                isCalculatingModal = false;
                $('#calculateBtnModal').prop('disabled', false).html('<i class="fas fa-calculator"></i> Calculate');
            }
        });
    }

    function displayCalculationModal(data) {
        const calc = data.calculation;
        const settings = data.settings;

        $('#availableCoinsModal').text(calc.available_coins);
        $('#minimumBillAmountModal').text(settings.minimum_bill_amount.toFixed(2));
        $('#discountPercentModal').text(settings.discount_percent.toFixed(2));
        $('#maxDiscountAmountModal').text(calc.max_discount_by_percent.toFixed(2));
        $('#redeemableCoinsModal').text(calc.redeemable_coins);
        $('#discountAmountModal').text(calc.discount_amount.toFixed(2));
        $('#finalAmountModal').text(calc.final_amount.toFixed(2));

        // If coins are redeemed for discount, automatically set Redeemed to true
        if (calc.redeemable_coins > 0 && calc.discount_amount > 0) {
            $('#redeemed').prop('checked', true);
        }

        // Eligibility alert
        if (calc.is_eligible) {
            $('#eligibilityAlertModal').removeClass('alert-warning alert-danger').addClass('alert-success')
                .html('<i class="fas fa-check-circle"></i> ' + calc.eligibility_message);
        } else {
            $('#eligibilityAlertModal').removeClass('alert-success alert-warning').addClass('alert-danger')
                .html('<i class="fas fa-exclamation-circle"></i> ' + calc.eligibility_message);
        }

        $('#calculationResultsModal').show();
    }

    // Recalculate when redeemed checkbox changes
    $('#redeemed').on('change', function() {
        const billAmount = parseFloat($('#billAmountModal').val());
        const userId = $('#selectedUserId').val();
        if (billAmount && billAmount > 0 && userId) {
            performCalculationModal(billAmount);
        }
    });

    // Enable save button when bill amount and transaction ID are filled
    $('#txnCodeModal').on('input change', function() {
        const userId = $('#selectedUserId').val();
        const billAmount = $('#billAmountModal').val();
        const txnCode = $(this).val()?.trim();
        if (userId && billAmount && txnCode) {
            $('#saveVisitorBtn').prop('disabled', false);
        } else {
            $('#saveVisitorBtn').prop('disabled', true);
        }
    });

    // Save visitor and transaction
    $('#saveVisitorBtn').on('click', function() {
        if (!selectedUser) {
            alert('Please select a user');
            return;
        }

        const userId = $('#selectedUserId').val();
        const billAmount = parseFloat($('#billAmountModal').val());
        const txnCode = $('#txnCodeModal').val()?.trim();
        const visitedOn = $('#visitedOn').val();
        const redeemed = $('#redeemed').is(':checked') ? 1 : 0;

        if (!userId) {
            alert('Please select a user');
            return;
        }

        if (!billAmount || billAmount <= 0) {
            alert('Please enter a valid bill amount');
            return;
        }

        if (!txnCode) {
            alert('Please enter a Transaction ID');
            return;
        }

        if (!confirm('Are you sure you want to create this transaction?')) {
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

        $.ajax({
            url: "{{ route('store.visitors.add-transaction-with-user') }}",
            method: 'POST',
            data: {
                user_id: userId,
                bill_amount: billAmount,
                txn_code: txnCode,
                visited_on: visitedOn,
                redeemed: redeemed,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Transaction created successfully!');
                    $('#addVisitorModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    alert(response.message || 'Failed to create transaction');
                }
            },
            error: function(xhr) {
                let error = 'Failed to create transaction';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        // Show validation errors
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        error = errors.join('\n');
                    } else if (xhr.responseJSON.message) {
                        error = xhr.responseJSON.message;
                    }
                }
                alert(error);
            },
            complete: function() {
                $('#saveVisitorBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Add Transaction');
            }
        });
    });

    // Reset add visitor modal on close
    $('#addVisitorModal').on('hidden.bs.modal', function() {
        $('#addVisitorForm')[0].reset();
        $('#userSearch').val('');
        $('#selectedUserId').val('');
        $('#billAmountModal').val('');
        $('#txnCodeModal').val('');
        $('#calculationResultsModal').hide();
        $('#selectedUserInfo').hide();
        $('#userSearchResults').hide();
        $('#saveVisitorBtn').prop('disabled', true);
        $('#visitedOn').val('{{ date('Y-m-d') }}');
        selectedUser = null;
        if (calculateTimeoutModal) {
            clearTimeout(calculateTimeoutModal);
            calculateTimeoutModal = null;
        }
        isCalculatingModal = false;
    });
</script>
@endpush
@endsection


