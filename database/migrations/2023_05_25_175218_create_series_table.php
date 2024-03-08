<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->integer('service_provider_id')->index();
            $table->string('company_code', 10)->nullable()->default('');
            $table->string('prefix', 3)->nullable()->default('')->index();
            $table->string('suffix', 3)->nullable()->default('')->index();
            $table->string('series_no', 50)->index();
            $table->string('complete_no', 50)->unique()->index();
            $table->enum('series_type', ['POLICY', 'COC', 'OR'])->nullable()->default('OR');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('series');
    }
}
