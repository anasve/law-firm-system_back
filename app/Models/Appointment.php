<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consultation_id',
        'availability_id',
        'lawyer_id',
        'client_id',
        'subject',
        'description',
        'datetime',
        'type',
        'notes',
        'status',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function availability()
    {
        return $this->belongsTo(LawyerAvailability::class, 'availability_id');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('datetime', '>=', now())
            ->where('status', '!=', 'cancelled');
    }

    public function scopeForLawyer($query, $lawyerId)
    {
        return $query->where('lawyer_id', $lawyerId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Accessor للتحقق من أن الموعد هو طلب وقت مخصص
    public function getIsCustomTimeRequestAttribute()
    {
        return $this->availability_id === null;
    }

    // Scope للمواعيد بوقت مخصص
    public function scopeCustomTimeRequests($query)
    {
        return $query->whereNull('availability_id');
    }

    // التحقق من انتهاء الموعد وتحديثه تلقائياً
    public function checkAndMarkAsDone()
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        $now = \Carbon\Carbon::now();
        $endTime = null;

        // إذا كان للموعد availability، استخدم end_time
        if ($this->availability_id && $this->availability) {
            $availability = $this->availability;
            $date = $availability->date;
            
            // معالجة end_time (قد يكون H:i أو H:i:s)
            $endTimeStr = $availability->end_time;
            if (strlen($endTimeStr) == 5) {
                // إذا كان بصيغة H:i، أضف :00
                $endTimeStr .= ':00';
            }
            
            try {
                $endTime = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $date->format('Y-m-d') . ' ' . $endTimeStr
                );
            } catch (\Exception $e) {
                // في حالة الخطأ، استخدم datetime + ساعة واحدة
                $endTime = \Carbon\Carbon::parse($this->datetime)->addHour();
            }
        } else {
            // إذا لم يكن له availability (custom time request)، استخدم datetime + ساعة واحدة كافتراضية
            $endTime = \Carbon\Carbon::parse($this->datetime)->addHour();
        }

        // إذا انتهى وقت الموعد، قم بتحديث حالته
        if ($endTime && $endTime->lt($now)) {
            $this->status = 'done';
            $this->save();
            return true;
        }

        return false;
    }

    // تحديث جميع المواعيد المنتهية
    public static function markCompletedAppointments()
    {
        $appointments = self::with('availability')
            ->where('status', 'confirmed')
            ->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            if ($appointment->checkAndMarkAsDone()) {
                $count++;
            }
        }

        return $count;
    }
}

