<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('normal')->after('payment_method'); // normal, repeat, recurring
            $table->foreignId('parent_order_id')->nullable()->after('order_type')->constrained('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['parent_order_id']);
            $table->dropColumn(['order_type', 'parent_order_id']);
        });
    }
};
