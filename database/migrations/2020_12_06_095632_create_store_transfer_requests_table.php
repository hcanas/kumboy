<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTransferRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('target_id');
            $table->string('attachment');

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
        Schema::dropIfExists('store_transfer_requests');
    }
}
