<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStoreRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code')->unique();
            $table->string('type');
            $table->string('status');
            $table->unsignedBigInteger('evaluated_by')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('evaluated_by')->references('id')->on('users');
            $table->engine = 'InnoDB';
        });

        DB::statement('ALTER TABLE store_requests ADD FULLTEXT (code, type, status)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_requests');
    }
}
