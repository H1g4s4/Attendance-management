<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceIdToRequestLogsTable extends Migration
{
    public function up()
    {
        Schema::table('request_logs', function (Blueprint $table) {
            // nullable にしておくと既存レコードへの影響を抑えられます
            $table->foreignId('attendance_id')
                    ->nullable()
                    ->constrained('attendances')
                    ->cascadeOnDelete()
                    ->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('request_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_id');
        });
    }
}
