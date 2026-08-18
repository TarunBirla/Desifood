<?php

namespace App\Console\Commands;

use App\Mail\NextMonthOrderReadyMail;
use App\Models\RecurringOrder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRecurringOrderReminders extends Command
{
    protected $signature = 'recurring:notify-due';
    protected $description = 'Send email reminders to users whose next-month recurring orders are ready for review';

    public function handle()
    {
        $targetMonth = date('Y-m'); // current month is now due
        $targetMonthName = date('F Y');

        $activeUserIds = RecurringOrder::where('target_month', $targetMonth)
            ->where('status', 'active')
            ->where('notified_at_due', false)
            ->pluck('user_id')
            ->unique();

        $sentCount = 0;

        foreach ($activeUserIds as $userId) {
            $user = User::find($userId);
            if (!$user || !$user->email) continue;

            $items = RecurringOrder::with(['product', 'variant'])
                ->where('user_id', $userId)
                ->where('target_month', $targetMonth)
                ->where('status', 'active')
                ->get();

            if ($items->isEmpty()) continue;

            $totalEst = $items->sum(function($i) {
                $price = $i->variant ? $i->variant->effective_price : ($i->product ? $i->product->effective_price : $i->unit_price);
                return $price * $i->quantity;
            });

            try {
                Mail::to($user->email)->send(new NextMonthOrderReadyMail($user, $items, $targetMonthName, $totalEst));
                RecurringOrder::whereIn('id', $items->pluck('id'))->update(['notified_at_due' => true]);
                $sentCount++;
                $this->info("Reminder sent to {$user->email} for {$targetMonthName}.");
            } catch (\Exception $e) {
                $this->error("Failed sending email to {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Done! Sent {$sentCount} recurring order reminder email(s).");
        return Command::SUCCESS;
    }
}
