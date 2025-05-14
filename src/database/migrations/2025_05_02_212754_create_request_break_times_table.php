<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestBreakTimesTable extends Migration
{
    public function up()
    {
        Schema::create('request_break_times', function (Blueprint $table) {
            $table->id();
            // 申請ログの外部キー
            $table->foreignId('request_log_id')
                ->constrained('request_logs')
                ->cascadeOnDelete();
            $table->timestamp('break_start')->nullable();
            $table->timestamp('break_end')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('request_break_times');
    }
}
