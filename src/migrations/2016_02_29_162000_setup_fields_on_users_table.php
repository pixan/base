<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupFieldsOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar', 512)->nullable();
            $table->bigInteger('facebook_id')->nullable();

            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            
            // Fields for address
            $table->string('street_line_1')->nullable();
            $table->string('street_line_2')->nullable();
            $table->string('zip')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();

            // Fields for push notifications
            $table->string('ios_device_token')->nullable();
            $table->string('android_device_token')->nullable();

        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
        	$table->dropColumn('avatar');
        	$table->dropColumn('facebook_id');

        	$table->dropColumn('lat');
        	$table->dropColumn('lon');

        	$table->dropColumn('street_line_1');
        	$table->dropColumn('street_line_2');
        	$table->dropColumn('zip');
        	$table->dropColumn('region');
        	$table->dropColumn('city');
        	$table->dropColumn('state');
        	$table->dropColumn('country');

        	$table->dropColumn('ios_device_token');
        	$table->dropColumn('android_device_token');
        });
    }
}