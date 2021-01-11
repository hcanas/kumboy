<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('name');
            $table->unsignedSmallInteger('qty');
            $table->decimal('price', 15, 2);
            $table->string('main_category');
            $table->string('sub_category')->nullable();
            $table->unsignedBigInteger('sold')->default(0);
            $table->string('preview')->nullable();

            $table->foreign('store_id')->references('id')->on('stores');
            $table->engine = 'InnoDB';
        });

        DB::statement('ALTER TABLE products ADD FULLTEXT (name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
