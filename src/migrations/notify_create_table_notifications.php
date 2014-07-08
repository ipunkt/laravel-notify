<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class NotifyCreateTableNotifications extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Create Table Notfications
         */
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->text('job');
            $table->text('data');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * Create Table for Activities
         */
        Schema::create('notification_activities', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('notification_id');
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');

            /**
             * There is no foreign key set, so one can use his own database structure for UserInterface
             */
            $table->unsignedInteger('user_id')->nullable()->default(null)->index();

            $table->string('activity');

            $table->timestamps();

            $table->index(['notification_id', 'user_id']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notification_activities');
        Schema::drop('notifications');
    }

}
