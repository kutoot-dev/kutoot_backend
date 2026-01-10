<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ZohoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProductToZohoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Product $product) {}

    public function handle(ZohoService $zohoService)
    {
        // Prevent duplicate sync
        if ($this->product->zoho_item_id) {
            return;
        }

        $zohoItemId = $zohoService->createItem([
            'name'  => $this->product->name,
            'sku'   => $this->product->sku,
            'price' => $this->product->price,
        ]);

        $this->product->update([
            'zoho_item_id' => $zohoItemId,
        ]);
    }
}
