<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_inbox_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sender', 50);
            $table->text('message');
            $table->timestampTz('received_at')->nullable();
            $table->timestampTz('deleted_at');
            $table->timestampsTz();

            $table->index('sender');
            $table->index('received_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_inbox_logs');
    }
};
