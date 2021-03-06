<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create255Invoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('255_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_no');
            $table->integer('customer_id');
            $table->integer('payment_method_id');
            $table->integer('total');
            $table->integer('status')->default(0);
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
        Schema::drop('255_invoices');
    }
}
