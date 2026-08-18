<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_price',
        'selected_month',
        'target_month',
        'status',
        'completed_order_id',
        'notified_at_due'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function completedOrder()
    {
        return $this->belongsTo(Order::class, 'completed_order_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }
}
