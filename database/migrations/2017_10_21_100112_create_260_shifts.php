<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create260Shifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('260_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('from');
            $table->string('to')-> status()->integer()->default(1);
            $table->string('working_days');
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
        Schema::drop('260_shifts');
    }
}
