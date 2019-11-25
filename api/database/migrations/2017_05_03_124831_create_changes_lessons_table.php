<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangesLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changes_lessons', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('change_id')->unsigned();

            $table->string('class');
            $table->integer('index');
            $table->string('subject');

            $table->string('teacher')->nullable();
            $table->string('room')->nullable();
            $table->string('info')->nullable();

            $table->string('group')->nullable();

            $table->timestamps();

            $table->foreign('change_id')->references('id')->on('changes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('changes_lessons');
    }
}
