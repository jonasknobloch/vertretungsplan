<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules_lessons', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('schedule_school_id')->unsigned();

            $table->integer('day');
            $table->string('class');
            $table->integer('index');
            $table->string('week')->nullable();

            $table->string('subject');
            $table->string('teacher');
            $table->string('room');

            $table->string('group')->nullable();
            $table->string('coupling')->nullable();

            $table->timestamps();

            $table->foreign('schedule_school_id')->references('school_id')->on('schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules_lessons');
    }
}
