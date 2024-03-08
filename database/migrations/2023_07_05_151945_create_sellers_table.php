<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->integer('service_provider_id')->index();
            $table->string('region_code')->nullable();
            $table->string('registered_name')->nullable();
            $table->string('registered_address')->nullable();
            $table->string('business_name')->nullable();
            $table->string('tin')->nullable()->unique();
            $table->enum('vat_type', ['V', 'NV'])->nullable()->default('V');
            $table->enum('type', ['CORP', 'INDIVIDUAL'])->nullable()->default('INDIVIDUAL');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->decimal('sales_per_anum',13,2)->nullable()->default(0);
            $table->tinyInteger('has_cor')->default(0)->comment("for demo");
            $table->enum('eligible_witheld_seller', ["NONE", "ELIGIBLE", "ACTIVE"])->nullable()->default("NONE");
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
        Schema::dropIfExists('sellers');
    }
}
