<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create255OpeningMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('255_opening_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('opening_id');
            $table->string('key');
            $table->text('value');
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
        Schema::drop('255_opening_meta');
    }
}
