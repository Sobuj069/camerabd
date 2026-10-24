<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WooCommerceService;

class WooCommerceSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:sync {--type=all : Type of sync (all, products, orders)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products and orders from WooCommerce REST API';

    /**
     * Execute the console command.
     */
    public function handle(WooCommerceService $wooService)
    {
        $type = $this->option('type');
        $this->info("Starting WooCommerce Sync process (Type: {$type})...");

        if (in_array($type, ['all', 'products'])) {
            $this->info("Syncing Products...");
            $prodResult = $wooService->syncProducts(100);
            if ($prodResult['success']) {
                $this->info($prodResult['message']);
            } else {
                $this->error($prodResult['message']);
            }
        }

        if (in_array($type, ['all', 'orders'])) {
            $this->info("Syncing Orders...");
            $orderResult = $wooService->syncOrders(50);
            if ($orderResult['success']) {
                $this->info($orderResult['message']);
            } else {
                $this->error($orderResult['message']);
            }
        }

        $this->info("WooCommerce Sync process completed!");
        return 0;
    }
}
