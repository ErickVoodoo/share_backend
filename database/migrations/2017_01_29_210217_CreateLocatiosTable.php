<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocatiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function($table) {
          $table->string('id');
          $table->integer('user_id');
          $table->string('city');
          $table->string('street');
          $table->primary(['id']);
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('products', function($table) {
          $table->string('location_id')->after('user_id')->nullable();

          $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
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
            $table->dropColumn('location_id');
        });

        Schema::dropIfExists('locations');
    }
}
