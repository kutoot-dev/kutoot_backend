<?php

namespace App\Http\Controllers;

use App\Models\CouponCampaign;
use App\Models\CouponTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * @group Coupon Ticket
 */
class CouponTicketController extends Controller
{
    public function index(CouponCampaign $campaign)
    {
        $tickets = $campaign->tickets()->latest()->paginate(20);
        return view('admin.tickets.index', compact('campaign', 'tickets'));
    }

    public function generate(Request $request, CouponCampaign $campaign)
{
    $request->validate([
        'quantity' => 'required|integer|min:1|max:1000', // limit for performance
    ]);

    $quantity = $request->input('quantity');
    $generated = 0;
    $attempts = 0;

    // Safety limit to prevent infinite loops
    $maxAttempts = $quantity * 10;

    while ($generated < $quantity && $attempts < $maxAttempts) {
        $attempts++;

        $ticketNumbers = $this->generateUniqueTicketNumbers($campaign);
        if (!$ticketNumbers) continue; // skip duplicate

        $ticketCode = $campaign->series_prefix . '-' . implode('-', $ticketNumbers);
        $ticketHash = md5(implode(',', $ticketNumbers));

        try {
            CouponTicket::create([
                'campaign_id' => $campaign->id,
                'ticket_code' => $ticketCode,
                'ticket_hash' => $ticketHash,
                'issued_at' => now(),
            ]);

            $generated++;
        } catch (\Throwable $e) {
            // Duplicate due to race condition? Just skip.
            continue;
        }
    }

    // Update campaign ticket counter
    $campaign->increment('tickets_issued', $generated);

    return back()->with('success', "$generated tickets generated!");
}


private function generateUniqueTicketNumbers(CouponCampaign $campaign)
{
    $range = range($campaign->number_min, $campaign->number_max);
    $count = $campaign->numbers_per_ticket;

    // Pick N unique numbers
    if (count($range) < $count) return null;

    $selected = collect($range)->random($count)->sort()->values();

    $hash = md5($selected->implode(','));

    // Check for duplicate
    $exists = CouponTicket::where('campaign_id', $campaign->id)
                ->where('ticket_hash', $hash)
                ->exists();

    return $exists ? null : $selected->map(fn($n) => str_pad($n, 2, '0', STR_PAD_LEFT))->toArray();
}

}
