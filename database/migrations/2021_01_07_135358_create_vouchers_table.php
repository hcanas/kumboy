<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('code');
            $table->string('type');
            $table->unsignedInteger('amount');
            $table->text('categories');
            $table->unsignedInteger('limit_per_user');
            $table->unsignedInteger('qty');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('status');

            $table->foreign('store_id')->references('id')->on('stores');
            $table->engine = 'InnoDB';
        });

        DB::statement('ALTER TABLE vouchers ADD FULLTEXT (code, categories)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_codes');
    }
}
