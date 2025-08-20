<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Notifications\SyncSummaryNotification;
use App\Models\SyncLog;

class SyncSummaryNotificationTest extends TestCase
{
    public function test_notification_mail_content()
    {
        $log = new SyncLog([
            'fetched' => 20,
            'created' => 10,
            'updated' => 5,
            'skipped' => 3,
            'failed' => 2,
            'status' => 'completed',
        ]);

        $notification = new SyncSummaryNotification($log);
        $mail = $notification->toMail((object) []);

        $this->assertStringContainsString('Product Sync Summary', $mail->subject);
        $this->assertStringContainsString('Fetched: 20', implode("\n", $mail->introLines));
        $this->assertStringContainsString('Created: 10', implode("\n", $mail->introLines));
        $this->assertStringContainsString('Status: Completed', implode("\n", $mail->introLines));
    }
}
