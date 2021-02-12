<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreApplicationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_application_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('name');
            $table->string('contact_number');
            $table->string('address_line');
            $table->string('map_coordinates');
            $table->string('map_address');
            $table->date('open_until');
            $table->string('attachment');

            $table->foreign('ref_no')->references('ref_no')->on('store_requests');
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
        Schema::dropIfExists('store_application_requests');
    }
}
