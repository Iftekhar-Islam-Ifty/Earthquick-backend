<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Console\Command;

class BackfillLegacyProductVendorsCommand extends Command
{
    protected $signature = 'catalog:backfill-legacy-vendors
                            {vendor=nous-telos : Vendor slug to assign}
                            {--apply : Apply the ownership assignment}
                            {--force : Skip the confirmation prompt when applying}';

    protected $description = 'Review or explicitly assign products without a vendor';

    public function handle(): int
    {
        $vendor = Vendor::where('slug', $this->argument('vendor'))->first();
        if (! $vendor) {
            $this->error('Vendor not found: '.$this->argument('vendor'));
            return self::FAILURE;
        }

        $products = Product::whereNull('vendor_id')->orderBy('id')->get(['id', 'sku', 'name']);
        $this->info("Unassigned products: {$products->count()}");

        if ($products->isEmpty()) {
            return self::SUCCESS;
        }

        $this->table(['ID', 'SKU', 'Product'], $products->map(fn ($product) => [
            $product->id,
            $product->sku ?: '—',
            $product->name,
        ])->all());

        if (! $this->option('apply')) {
            $this->comment('Report only. Re-run with --apply after reviewing ownership.');
            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Assign all {$products->count()} products to {$vendor->name}?")) {
            $this->comment('No changes made.');
            return self::SUCCESS;
        }

        Product::whereNull('vendor_id')->update(['vendor_id' => $vendor->id]);
        $this->info("Assigned {$products->count()} products to {$vendor->name}.");

        return self::SUCCESS;
    }
}
