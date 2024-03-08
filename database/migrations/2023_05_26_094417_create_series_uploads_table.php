<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeriesUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->index();
            $table->integer('service_provider_id')->index();
            $table->string('original_filename', 25);
            $table->string('filename', 25);
            $table->integer('total_success')->nullable()->default(0);
            $table->integer('total_failed')->nullable()->default(0);
            $table->datetime('processed_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->enum('status', ['PENDING', 'PROCESSING', 'COMPLETED'])->default('PENDING');
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
        Schema::dropIfExists('series_uploads');
    }
}
