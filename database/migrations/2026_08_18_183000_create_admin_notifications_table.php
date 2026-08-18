<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('new_order');
            $table->string('title');
            $table->text('message');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
            $table->string('order_number')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->decimal('grand_total', 10, 2)->default(0.00);
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
