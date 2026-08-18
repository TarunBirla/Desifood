<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'shipping_address_id', 'shipping_address_json',
        'order_status', 'payment_status', 'payment_method', 'order_type', 'parent_order_id',
        'subtotal', 'discount_amount', 'coupon_code', 'shipping_fee', 'tax_amount', 'grand_total',
        'tracking_number', 'delivery_partner', 'customer_note', 'admin_note',
        'cancellation_reason', 'paid_at', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'shipping_address_json' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentOrder()
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    public function repeatOrders()
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    public function recurringOrders()
    {
        return $this->hasMany(RecurringOrder::class, 'completed_order_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }
}
