<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NextMonthOrderReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection $recurringOrders;
    public string $targetMonthName;
    public float $totalEst;

    public function __construct(User $user, Collection $recurringOrders, string $targetMonthName, float $totalEst)
    {
        $this->user = $user;
        $this->recurringOrders = $recurringOrders;
        $this->targetMonthName = $targetMonthName;
        $this->totalEst = $totalEst;
    }

    public function build()
    {
        return $this->subject("Your {$this->targetMonthName} Recurring Order Is Ready to Review")
                    ->view('emails.next_month_order_ready');
    }
}
