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
        Schema::create('file_handler_s3_uploads', function (Blueprint $table) {
            $table->id();
            $table->longText('path');
            $table->longText('signed_url')->nullable();
            $table->dateTime('signed_url_expiry')->nullable();
            $table->boolean('public')->default(false);
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
        Schema::dropIfExists('file_handler_s3_uploads');
    }
};
