<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->integer('service_provider_id')->index();
            $table->decimal('amount',13,2)->default(0);
            $table->decimal('min',13,2)->default(0);
            $table->decimal('max',13,2)->default(0);
            $table->enum('amount_type', ['PERCENTAGE', 'FIXED','CAPPED'])->default('PERCENTAGE');
            $table->enum('type', ['TRANSACTION', 'SERVICE', 'COMMISSION'])->default('TRANSACTION');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->tinyInteger('round_decimal')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees');
    }
}
