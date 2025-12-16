<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MarkCompletedAppointments extends Command
{
    protected $signature = 'appointments:mark-completed';
    protected $description = 'Mark appointments as done when their end time has passed';

    public function handle()
    {
        // استخدام method من الـ model لتحديث المواعيد
        $count = Appointment::markCompletedAppointments();

        if ($count > 0) {
            $this->info("تم تحديث {$count} موعد إلى حالة 'done'");
        } else {
            $this->info('لا توجد مواعيد تحتاج إلى تحديث');
        }

        return 0;
    }
}

