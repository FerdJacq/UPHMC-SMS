<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersFilesTable extends Migration
{
 
   public function up()
    {
        Schema::create('sellers_files', function (Blueprint $table) {
            $table->id();
            $table->integer('sellers_id')->index();
            $table->string('original_filename', 25);
            $table->string('filename', 25);
            $table->enum('file_type', ['BIR Forms', 'Account Information Forms', 'Application Forms', 'Certificates', 'Documentary Stamp Tax Return', 'Excise Tax Return', 'Income Tax Return', 'Legal Forms'])
                ->nullable()->default('BIR Forms');
            $table->string('mime_type')->default('')->nullable();
            $table->string('extension')->default('')->nullable()->index();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }   
    public function down()
    {
        Schema::dropIfExists('sellers_files');
    }
}
