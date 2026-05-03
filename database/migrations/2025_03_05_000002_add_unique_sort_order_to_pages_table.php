<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Resolve duplicate sort_order values before adding unique index
        $maxOrder = DB::table('pages')->max('sort_order') ?? 0;
        $duplicateOrders = DB::table('pages')
            ->select('sort_order')
            ->groupBy('sort_order')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('sort_order');

        foreach ($duplicateOrders as $order) {
            $pages = DB::table('pages')->where('sort_order', $order)->orderBy('created_at')->get();
            foreach ($pages->skip(1) as $index => $page) {
                $maxOrder++;
                DB::table('pages')->where('id', $page->id)->update(['sort_order' => $maxOrder]);
            }
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->unique('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique(['sort_order']);
        });
    }
};
