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

        try {
            $zohoItemId = $zohoService->createItem([
                'name'  => $this->product->name,
                'sku'   => $this->product->sku,
                'price' => $this->product->price,
            ]);

            $this->product->update([
                'zoho_item_id' => $zohoItemId,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Zoho sync failed for product', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);

            // Don't re-throw - allow the job to complete without failing the queue
            // Zoho sync can be retried manually via zoho:sync-products command
        }
    }
}
