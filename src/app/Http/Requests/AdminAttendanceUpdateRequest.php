<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',
            'note' => 'required|string',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end' => 'nullable|date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間もしくは退勤時間が不適切な値です。',
            'end_time.required' => '出勤時間もしくは退勤時間が不適切な値です。',
            'end_time.after_or_equal' => '出勤時間もしくは退勤時間が不適切な値です。',
            'note.required' => '備考を記入してください。',
            'breaks.*.start.date_format' => '休憩時間が勤務時間外です。',
            'breaks.*.end.date_format' => '休憩時間が勤務時間外です。',
        ];
    }

}
