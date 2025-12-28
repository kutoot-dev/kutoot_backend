<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Zoho\ZohoClient;
use App\Models\Product;

class SyncZohoProducts extends Command
{
    protected $signature = 'zoho:sync-products';
    protected $description = 'Sync products & inventory from Zoho Inventory';

    public function handle(ZohoClient $zoho)
    {
        $this->info('Fetching products from Zoho Inventory...');

        $response = $zoho->get('/items');

        if (isset($response['code']) && $response['code'] != 0) {
            $this->error($response['message'] ?? 'Zoho API error');
            return;
        }

        if (empty($response['items'])) {
            $this->warn('No items returned from Zoho Inventory');
            return;
        }

        foreach ($response['items'] as $item) {

            if (empty($item['sku'])) {
                $this->warn("Zoho item skipped (SKU missing): {$item['name']}");
                continue;
            }

            $product = Product::updateOrCreate(
                ['sku' => $item['sku']], // 🔑 UNIQUE
                [
                    'name'         => $item['name'],
                    'price'        => $item['rate'],
                    'stock'        => $item['available_stock'] ?? 0,
                    'zoho_item_id' => $item['item_id'], // 🔥 MOST IMPORTANT
                    'status'       => 1,
                ]
            );

            $this->info(
                "Synced SKU {$item['sku']} → Zoho Item {$item['item_id']} (Product ID: {$product->id})"
            );
        }

        $this->info('Zoho products synced successfully.');
    }
}
