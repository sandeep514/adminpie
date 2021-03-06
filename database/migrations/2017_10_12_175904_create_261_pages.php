<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create261Pages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('261_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('tags')->nullable();
            $table->string('categories')->nullable();
            $table->string('post_type')->nullable();
            $table->string('attachments')->nullable();
            $table->string('version')->nullable();
            $table->string('revision')->nullable();
            $table->string('created_by')->nullable();
            $table->string('post_status')->nullable();
            $table->integer('status')->default(1);
            $table->string('type');
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
        Schema::drop('261_pages');
    }
}
