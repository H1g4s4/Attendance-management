<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'attendance_date',
        'start_time',
        'end_time',
        'note',
        'is_pending',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'start_time'      => 'datetime',
        'end_time'        => 'datetime',
        'is_pending'      => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(RequestBreakTime::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}