<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->string('selected_month'); // e.g. 2026-08
            $table->string('target_month');   // e.g. 2026-09
            $table->enum('status', ['active', 'cancelled', 'ordered', 'skipped'])->default('active');
            $table->foreignId('completed_order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->boolean('notified_at_due')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'target_month'], 'user_prod_target_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_orders');
    }
};
