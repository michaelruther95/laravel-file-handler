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
        Schema::create('file_handler_media_usages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('media_id')
                ->unsigned()
                ->nullable();
            $table->foreign('media_id')
                ->references('id')->on('file_handler_files')
                ->onDelete('cascade');
            $table->string('table_used_on');
            $table->string('table_column_used_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_handler_media_usages');
    }
};
