<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可が必要ならここでチェック
    }

    public function rules(): array
    {
        return [
            'attendance_date'    => ['required', 'date'],
            'start_time'         => ['nullable', 'date_format:H:i'],
            'end_time'           => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'note'               => ['required', 'string', 'max:255'],
            'breaks'             => ['array'],
            'breaks.*.start'     => ['nullable', 'date_format:H:i'],
            'breaks.*.end'       => ['nullable', 'date_format:H:i', 'after_or_equal:breaks.*.start'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required'            => '備考を記入してください',
            'end_time.after_or_equal'  => '退勤時刻は出勤時刻以降を指定してください',
            // 他メッセージは必要に応じて追加…
        ];
    }
}
