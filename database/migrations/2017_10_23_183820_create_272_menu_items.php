<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create272MenuItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('272_menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('link');
            $table->integer('parent')->default(0);
            $table->integer('sort')->default(0);
            $table->string('class')->nullable();
            $table->integer('menu');
            $table->integer('depth')->default(0);
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
        Schema::drop('272_menu_items');
    }
}
