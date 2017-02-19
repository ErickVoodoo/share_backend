<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function($table) {
          $table->string('id');
          $table->string('value')->nullable();
          $table->string('promo')->nullable();

          $table->primary(['id']);
        });

        Schema::table('products', function($table) {
          $table->string('discount_id')->after('delivers_id')->nullable();
          $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function($table) {
            $table->dropColumn('discount_id');
        });

        Schema::dropIfExists('discounts');
    }
}
