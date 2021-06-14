<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_video', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_user_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('video_id')->unsigned();
            $table->enum('type', ['saved','tag'])->nullable();
            $table->enum('status', ['0','1'])->nullable();
            $table->timestamps();

            $table->foreign('parent_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_video');
    }
}
