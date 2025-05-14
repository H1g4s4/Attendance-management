<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToRequestLogsTable extends Migration
{
    public function up()
    {
        Schema::table('request_logs', function (Blueprint $table) {
            // 申請対象日
            if (! Schema::hasColumn('request_logs', 'attendance_date')) {
                $table->date('attendance_date')->after('user_id');
            }
            // 打刻修正用の開始・終了時刻
            if (! Schema::hasColumn('request_logs', 'start_time')) {
                $table->timestamp('start_time')->nullable()->after('attendance_date');
            }
            if (! Schema::hasColumn('request_logs', 'end_time')) {
                $table->timestamp('end_time')->nullable()->after('start_time');
            }
            // 備考
            if (! Schema::hasColumn('request_logs', 'note')) {
                $table->text('note')->nullable()->after('end_time');
            }
            // 承認フラグ（なければ追加。ただし既にあればスキップ）
            if (! Schema::hasColumn('request_logs', 'is_pending')) {
                $table->boolean('is_pending')->default(true)->after('note');
            }
        });
    }

    public function down()
    {
        Schema::table('request_logs', function (Blueprint $table) {
            // 追加したカラムをまとめて削除
            $cols = ['attendance_date', 'start_time', 'end_time', 'note', 'is_pending'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('request_logs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
