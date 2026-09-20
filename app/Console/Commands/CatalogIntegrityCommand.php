<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;
use Illuminate\Console\Command;

class CatalogIntegrityCommand extends Command
{
    protected $signature = 'catalog:integrity {--json : Render machine-readable JSON output}';

    protected $description = 'Report catalog ownership, status and reference integrity without changing data';

    public function handle(): int
    {
        $report = [
            'products_total' => Product::count(),
            'products_unassigned' => Product::whereNull('vendor_id')->count(),
            'products_inactive' => Product::where('is_active', false)->count(),
            'products_missing_sku' => Product::whereNull('sku')->orWhere('sku', '')->count(),
            'products_inactive_vendor' => Product::whereHas('vendor', fn ($q) => $q->where('is_active', false))->count(),
            'products_invalid_subcategory' => Product::whereNotNull('subcategory_id')
                ->whereHas('subcategory', function ($query) {
                    $query->whereColumn('subcategories.category_id', '!=', 'products.category_id');
                })->count(),
            'vendors_total' => Vendor::count(),
            'vendors_inactive' => Vendor::where('is_active', false)->count(),
            'categories_total' => Category::count(),
            'subcategories_total' => Subcategory::count(),
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(['Metric', 'Count'], collect($report)
                ->map(fn ($value, $key) => [$key, $value])->values()->all());
        }

        return self::SUCCESS;
    }
}
