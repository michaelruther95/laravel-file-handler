<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_handler_files', function (Blueprint $table) {
            $table->bigInteger('s3_upload_id')
                ->unsigned()
                ->nullable();
            $table->foreign('s3_upload_id')
                ->references('id')->on('file_handler_s3_uploads')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_handler_files', function (Blueprint $table) {
            //
        });
    }
};
