<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos_comment', function (Blueprint $table) {
            $table->id();
            $table->integer('video_id');
            $table->string('video_name');
            $table->string('video_title');
            $table->integer('views');
            $table->integer('likes');
            $table->integer('shares');
            $table->integer('save')->nullable();
            $table->integer('report');
            $table->string('location');
            $table->enum('status', ['0','1'])->nullable();
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
        Schema::dropIfExists('videos_comment');
    }
}
