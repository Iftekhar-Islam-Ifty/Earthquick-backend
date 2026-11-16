<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('order_return_requests')
            ->where('return_shipping_payer', 'earthquick')
            ->update(['return_shipping_payer' => 'rthquick']);
    }

    public function down(): void
    {
        DB::table('order_return_requests')
            ->where('return_shipping_payer', 'rthquick')
            ->update(['return_shipping_payer' => 'earthquick']);
    }
};
