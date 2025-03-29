<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'is_on_break',
    ];

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
