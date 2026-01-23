<?php

namespace App\Exports;

use App\Models\CoinLedger;
use App\Services\CoinLedgerService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoinLedgerExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Build the query for export.
     */
    public function query()
    {
        $query = CoinLedger::with('user')->orderBy('created_at', 'desc');

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (!empty($this->filters['entry_type'])) {
            $query->where('entry_type', $this->filters['entry_type']);
        }

        if (!empty($this->filters['coin_category'])) {
            $query->where('coin_category', $this->filters['coin_category']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    /**
     * Column headings for the export.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'User ID',
            'User Name',
            'User Email',
            'Entry Type',
            'Coin Category',
            'Coins In',
            'Coins Out',
            'Net Change',
            'Expiry Date',
            'Reference ID',
            'Zoho Account Type',
            'Coin Value (₹)',
            'Amount Value (₹)',
        ];
    }

    /**
     * Map each row for export.
     */
    public function map($entry): array
    {
        $coinValue = config('kutoot.coin_value', 0.25);
        $netChange = $entry->coins_in - $entry->coins_out;

        return [
            $entry->id,
            $entry->created_at->format('Y-m-d H:i:s'),
            $entry->user_id,
            $entry->user->name ?? 'N/A',
            $entry->user->email ?? 'N/A',
            $this->formatEntryType($entry->entry_type),
            $entry->coin_category,
            $entry->coins_in,
            $entry->coins_out,
            $netChange,
            $entry->expiry_date ? $entry->expiry_date->format('Y-m-d') : 'No Expiry',
            $entry->reference_id ?? '-',
            CoinLedgerService::getZohoAccountType($entry),
            $coinValue,
            abs($netChange) * $coinValue,
        ];
    }

    /**
     * Format entry type for readability.
     */
    protected function formatEntryType(string $type): string
    {
        $types = [
            CoinLedger::TYPE_PAID_CREDIT => 'Paid Coin Credit',
            CoinLedger::TYPE_REWARD_CREDIT => 'Reward Coin Credit',
            CoinLedger::TYPE_REDEEM => 'Coin Redeem',
            CoinLedger::TYPE_EXPIRE => 'Coin Expire',
            CoinLedger::TYPE_REVERSAL => 'Reversal',
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Style the worksheet.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Bold header row
            1 => ['font' => ['bold' => true]],
        ];
    }
}
