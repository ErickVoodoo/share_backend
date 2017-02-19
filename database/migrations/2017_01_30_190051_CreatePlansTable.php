<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function($table) {
          $table->string('id');
          $table->float('cost');
          $table->integer('period');
          $table->string('name');

          $table->primary(['id']);
        });

        Schema::table('users', function($table) {
          $table->string('plan_id')->nullable();

          $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('plan_id');
        });

        Schema::dropIfExists('plans');
    }
}
