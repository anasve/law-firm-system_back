<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\Consultation\AppointmentReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send appointment reminders (24h and 1h before)';

    public function handle()
    {
        $now = Carbon::now();

        // تذكير قبل 24 ساعة
        $appointments24h = Appointment::where('status', 'confirmed')
            ->whereBetween('datetime', [
                $now->copy()->addHours(24)->subMinutes(5),
                $now->copy()->addHours(24)->addMinutes(5)
            ])
            ->get();

        foreach ($appointments24h as $appointment) {
            $appointment->client->notify(new AppointmentReminderNotification($appointment, '24h'));
            $appointment->lawyer->notify(new AppointmentReminderNotification($appointment, '24h'));
        }

        // تذكير قبل ساعة واحدة
        $appointments1h = Appointment::where('status', 'confirmed')
            ->whereBetween('datetime', [
                $now->copy()->addHour()->subMinutes(5),
                $now->copy()->addHour()->addMinutes(5)
            ])
            ->get();

        foreach ($appointments1h as $appointment) {
            $appointment->client->notify(new AppointmentReminderNotification($appointment, '1h'));
            $appointment->lawyer->notify(new AppointmentReminderNotification($appointment, '1h'));
        }

        $this->info('Sent ' . ($appointments24h->count() + $appointments1h->count()) . ' reminders');
    }
}

