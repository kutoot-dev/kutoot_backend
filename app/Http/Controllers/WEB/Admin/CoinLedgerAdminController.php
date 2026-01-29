<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinLedger;
use App\Models\User;
use App\Services\CoinLedgerService;
use App\Exports\CoinLedgerExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CoinLedgerAdminController extends Controller
{
    protected $coinService;

    public function __construct(CoinLedgerService $coinService)
    {
        $this->coinService = $coinService;
    }

    /**
     * Display the coin ledger dashboard/summary.
     */
    public function summary()
    {
        $summary = $this->coinService->getAdminSummary();
        $dailyFlow = $this->coinService->getDailyFlow(30);
        $coinValue = config('kutoot.coin_value', 0.25);
        $currencySymbol = config('kutoot.currency_symbol', '₹');

        return view('admin.coin_ledger.summary', compact(
            'summary',
            'dailyFlow',
            'coinValue',
            'currencySymbol'
        ));
    }

    /**
     * Display all ledger transactions with filters.
     */
    public function index(Request $request)
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'entry_type' => $request->input('entry_type'),
            'coin_category' => $request->input('coin_category'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'reference_id' => $request->input('reference_id'),
            'search' => $request->input('search'),
        ];

        $entries = $this->coinService->getLedgerQuery($filters)->paginate(50);

        // Get entry types and categories for filter dropdowns
        $entryTypes = [
            CoinLedger::TYPE_PAID_CREDIT => 'Paid Coin Credit',
            CoinLedger::TYPE_REWARD_CREDIT => 'Reward Coin Credit',
            CoinLedger::TYPE_REDEEM => 'Coin Redeem',
            CoinLedger::TYPE_EXPIRE => 'Coin Expire',
            CoinLedger::TYPE_REVERSAL => 'Reversal',
        ];

        $categories = [
            CoinLedger::CAT_PAID => 'Paid',
            CoinLedger::CAT_REWARD => 'Reward',
        ];

        return view('admin.coin_ledger.index', compact(
            'entries',
            'filters',
            'entryTypes',
            'categories'
        ));
    }

    /**
     * Display ledger for a specific user.
     */
    public function userLedger($userId)
    {
        $user = User::findOrFail($userId);
        $breakdown = $this->coinService->getBalanceBreakdown($userId);

        $entries = CoinLedger::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $entryTypes = [
            CoinLedger::TYPE_PAID_CREDIT => 'Paid Coin Credit',
            CoinLedger::TYPE_REWARD_CREDIT => 'Reward Coin Credit',
            CoinLedger::TYPE_REDEEM => 'Coin Redeem',
            CoinLedger::TYPE_EXPIRE => 'Coin Expire',
            CoinLedger::TYPE_REVERSAL => 'Reversal',
        ];

        return view('admin.coin_ledger.user_ledger', compact(
            'user',
            'breakdown',
            'entries',
            'entryTypes'
        ));
    }

    /**
     * Export ledger data to Excel.
     */
    public function export(Request $request)
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'entry_type' => $request->input('entry_type'),
            'coin_category' => $request->input('coin_category'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $filename = 'coin_ledger_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new CoinLedgerExport($filters), $filename);
    }

    /**
     * Manual admin credit (for adjustments).
     */
    public function manualCredit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'category' => 'required|in:PAID,REWARD',
            'reason' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($request->category === CoinLedger::CAT_PAID) {
            $this->coinService->creditPaid($user->id, $request->amount, 'ADMIN-' . auth('admin')->id());
        } else {
            $this->coinService->creditReward($user->id, $request->amount, 'ADMIN-' . auth('admin')->id());
        }

        $notification = 'Credited ' . $request->amount . ' ' . $request->category . ' coins to ' . $user->name;

        return redirect()->back()
            ->with('messege', $notification)
            ->with('alert-type', 'success');
    }

    /**
     * Get Zoho mapping reference for exports.
     */
    public function zohoMapping()
    {
        $mappings = [
            [
                'entry_type' => 'PAID_COIN_CREDIT',
                'coin_category' => 'PAID',
                'zoho_account' => 'Coin Liability',
                'description' => 'User purchased coins - creates liability on books',
            ],
            [
                'entry_type' => 'REWARD_COIN_CREDIT',
                'coin_category' => 'REWARD',
                'zoho_account' => 'Marketing Liability',
                'description' => 'Free/bonus coins given - marketing expense liability',
            ],
            [
                'entry_type' => 'COIN_REDEEM',
                'coin_category' => 'PAID',
                'zoho_account' => 'Discount Expense',
                'description' => 'Paid coins used - liability squared off as discount',
            ],
            [
                'entry_type' => 'COIN_REDEEM',
                'coin_category' => 'REWARD',
                'zoho_account' => 'Marketing Expense',
                'description' => 'Reward coins used - liability squared off as marketing expense',
            ],
            [
                'entry_type' => 'COIN_EXPIRE',
                'coin_category' => 'PAID/REWARD',
                'zoho_account' => 'Liability Write-off',
                'description' => 'Expired unused coins - liability written off (income)',
            ],
            [
                'entry_type' => 'COIN_REVERSAL',
                'coin_category' => 'PAID/REWARD',
                'zoho_account' => 'Reversal Adjustment',
                'description' => 'Manual adjustment or refund reversal',
            ],
        ];

        return view('admin.coin_ledger.zoho_mapping', compact('mappings'));
    }

    // =========================================================================
    // KUTOOT SYSTEM WALLET (LIABILITY MANAGEMENT)
    // =========================================================================

    /**
     * Display Kutoot system wallet dashboard.
     */
    public function systemWallet()
    {
        $systemBalance = $this->coinService->getSystemWalletBalance();
        $systemLedger = $this->coinService->getSystemWalletLedger(100);
        $coinValue = config('kutoot.coin_value', 0.25);
        $currencySymbol = config('kutoot.currency_symbol', '₹');

        $categories = [
            CoinLedger::CAT_PAID => 'Paid (Purchased)',
            CoinLedger::CAT_REWARD => 'Reward (Marketing)',
        ];

        return view('admin.coin_ledger.system_wallet', compact(
            'systemBalance',
            'systemLedger',
            'coinValue',
            'currencySymbol',
            'categories'
        ));
    }

    /**
     * Update total liability in Kutoot system wallet.
     */
    public function updateLiability(Request $request)
    {
        $request->validate([
            'category' => 'required|in:PAID,REWARD',
            'target_amount' => 'required|integer|min:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $entry = $this->coinService->updateSystemLiability(
                $request->category,
                $request->target_amount,
                $request->reason,
                auth('admin')->id()
            );

            $newBalance = $this->coinService->getSystemWalletBalance();
            $notification = 'Kutoot wallet updated. New ' . $request->category . ' balance: ' . number_format($newBalance[$request->category === 'PAID' ? 'paid' : 'reward']);

            return redirect()->back()
                ->with('messege', $notification)
                ->with('alert-type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('messege', $e->getMessage())
                ->with('alert-type', 'error');
        }
    }

    /**
     * Adjust liability by delta amount.
     */
    public function adjustLiability(Request $request)
    {
        $request->validate([
            'category' => 'required|in:PAID,REWARD',
            'adjustment' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $entry = $this->coinService->adjustSystemLiability(
                $request->category,
                $request->adjustment,
                $request->reason,
                auth('admin')->id()
            );

            $direction = $request->adjustment > 0 ? 'increased' : 'decreased';
            $notification = 'Kutoot wallet ' . $direction . ' by ' . number_format(abs($request->adjustment)) . ' ' . $request->category . ' coins.';

            return redirect()->back()
                ->with('messege', $notification)
                ->with('alert-type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('messege', $e->getMessage())
                ->with('alert-type', 'error');
        }
    }
}
