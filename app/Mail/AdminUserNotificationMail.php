<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminUserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $customSubject;
    public string $customMessage;

    public function __construct(User $user, string $customSubject, string $customMessage)
    {
        $this->user = $user;
        $this->customSubject = $customSubject;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        return $this->subject($this->customSubject)
                    ->view('emails.admin_user_notification');
    }
}
