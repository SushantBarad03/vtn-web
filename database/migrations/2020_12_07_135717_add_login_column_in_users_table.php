<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->after('profile_picture');
            $table->bigInteger('mobile_no')->nullable()->after('email');
            $table->bigInteger('fb_id')->nullable()->after('auto_play');
            $table->bigInteger('google_id')->nullable()->after('fb_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('mobile_no');
            $table->dropColumn('fb_id');
            $table->dropColumn('google_id');
        });
    }
}
