<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->unique();
            $table->string('contact_number');
            $table->string('address');
            $table->string('map_coordinates');
            $table->string('map_address');
            $table->date('open_until');
            $table->string('logo')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->engine = 'InnoDB';
        });

        DB::statement('ALTER TABLE stores ADD FULLTEXT (name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
