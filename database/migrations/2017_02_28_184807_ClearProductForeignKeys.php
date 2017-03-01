<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClearProductForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function($table) {
          $table->dropForeign(['deliver_id']);
          $table->dropForeign(['discount_id']);
          $table->foreign('deliver_id')->references('id')->on('delivers');
          $table->foreign('discount_id')->references('id')->on('discounts');
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
          $table->dropForeign(['deliver_id']);
          $table->dropForeign(['discount_id']);
          $table->foreign('deliver_id')->references('id')->on('delivers')->onUpdate('cascade')->onDelete('cascade');
          $table->foreign('discount_id')->references('id')->on('discounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
