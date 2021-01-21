<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('tracking_number')->unique();
            $table->string('contact_person');
            $table->string('contact_number');
            $table->string('address');
            $table->string('map_address');
            $table->string('map_coordinates');
            $table->string('voucher_code')->nullable();
            $table->unsignedDecimal('delivery_fee', 15, 2);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->foreign('user_id')->references('id')->on('users');
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
