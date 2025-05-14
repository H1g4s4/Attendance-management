<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestBreakTime extends Model
{
    // マスアサイン可能なカラム
    protected $fillable = [
        'request_log_id',
        'break_start',
        'break_end',
    ];

    // 日付キャスト
    protected $casts = [
        'break_start' => 'datetime',
        'break_end'   => 'datetime',
    ];

    // リクエストログへのリレーション
    public function requestLog()
    {
        return $this->belongsTo(RequestLog::class);
    }
}
