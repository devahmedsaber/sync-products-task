<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SyncLog;

class SyncSummaryNotification extends Notification
{
    use Queueable;

    protected SyncLog $log;

    public function __construct(SyncLog $log)
    {
        $this->log = $log;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Product Sync Summary')
            ->greeting('Hello Admin,')
            ->line('Here is the summary of the latest product sync:')
            ->line('Fetched: ' . $this->log->fetched)
            ->line('Created: ' . $this->log->created)
            ->line('Updated: ' . $this->log->updated)
            ->line('Skipped: ' . $this->log->skipped)
            ->line('Failed: ' . $this->log->failed)
            ->line('Status: ' . ucfirst($this->log->status))
            ->line('Thank you for using our system!');
    }
}
