<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('countries', function($table) {
        $table->string('id')->index();
        $table->string('name')->unique();
        $table->primary(['id']);
      });

      Schema::create('plans', function($table) {
        $table->string('id')->index();
        $table->string('cost');
        $table->string('period');
        $table->string('name');
        $table->primary(['id']);
      });

      Schema::create('users', function($table) {
        $table->string('id')->index();
        $table->string('country_id')->nullable();
        $table->string('plan_id');
        $table->string('login')->unique();
        $table->string('email')->unique();
        $table->string('password');
        $table->string('name')->nullable();
        $table->string('description')->nullable();
        $table->string('logo')->nullable();
        $table->timestamp('paid_at')->nullable();
        $table->timestamp('connected_at')->nullable();
        $table->timestamps();
        $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('cascade');
        $table->primary(['id']);
      });

      Schema::create('delivers', function($table) {
        $table->string('id')->index();
        $table->string('name')->unique();
        $table->primary(['id']);
      });

      Schema::create('categories', function($table) {
        $table->string('id')->index();
        $table->string('name')->unique();
        $table->primary(['id']);
      });

      Schema::create('discounts', function($table) {
        $table->string('id')->index();
        $table->string('value');
        $table->string('promo');
        $table->primary(['id']);
      });

      Schema::create('locations', function($table) {
        $table->string('id');
        $table->string('user_id');
        $table->string('city');
        $table->string('street');
        $table->string('lat');
        $table->string('lon');
        $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        $table->primary(['id']);
      });

      Schema::create('products', function($table) {
        $table->string('id')->index();
        $table->string('user_id');
        $table->string('category_id');
        $table->string('deliver_id')->nullable();
        $table->string('discount_id')->nullable();
        $table->string('location_id');
        $table->string('title');
        $table->string('description')->nullable();
        $table->string('price')->nullable();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('deliver_id')->references('id')->on('delivers')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign('category_id')->references('id')->on('categories');
        $table->foreign('discount_id')->references('id')->on('discounts')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign('location_id')->references('id')->on('locations');
        $table->primary(['id']);
      });

      Schema::create('images', function($table) {
        $table->string('id')->index();
        $table->string('product_id')->nullable();
        $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
      });

      Schema::create('links', function($table) {
        $table->string('product_id');
        $table->string('url')->unique();
        $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
      });

      Schema::create('password_resets', function($table) {
        $table->string('email')->index();
        $table->string('token')->index();
        $table->timestamp('created_at');
      });

      Schema::create('roles', function (Blueprint $table) {
        $table->string('id');
        $table->string('name')->unique();
        $table->string('display_name')->nullable();
        $table->string('description')->nullable();
        $table->timestamps();
        $table->primary(['id']);
      });

      Schema::create('role_user', function (Blueprint $table) {
        $table->string('user_id')->unsigned();
        $table->string('role_id')->unsigned();
        $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
        $table->primary(['user_id', 'role_id']);
      });

      Schema::create('permissions', function (Blueprint $table) {
        $table->string('id');
        $table->string('name')->unique();
        $table->string('display_name')->nullable();
        $table->string('description')->nullable();
        $table->timestamps();
        $table->primary(['id']);
      });

      Schema::create('permission_role', function (Blueprint $table) {
        $table->string('permission_id')->unsigned();
        $table->string('role_id')->unsigned();
        $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
        $table->primary(['permission_id', 'role_id']);
      });

      Schema::create('tags', function($table) {
        $table->string('id')->index();
        $table->string('name')->unique();
        $table->primary(['id']);
      });

      Schema::create('product_tags', function($table) {
        $table->string('product_id');
        $table->string('tag_id');
        $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign('tag_id')->references('id')->on('tags');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('images', function($table) {
        $table->dropForeign(['product_id']);
      });

      Schema::table('links', function($table) {
        $table->dropForeign(['product_id']);
      });

      Schema::table('locations', function($table) {
        $table->dropForeign(['user_id']);
      });

      Schema::table('permission_role', function($table) {
        $table->dropForeign(['permission_id']);
        $table->dropForeign(['role_id']);
      });

      Schema::table('product_tags', function($table) {
        $table->dropForeign(['product_id']);
        $table->dropForeign(['tag_id']);
      });

      Schema::table('products', function($table) {
        $table->dropForeign(['user_id']);
        $table->dropForeign(['deliver_id']);
        $table->dropForeign(['category_id']);
        $table->dropForeign(['discount_id']);
        $table->dropForeign(['location_id']);
      });

      Schema::table('role_user', function($table) {
        $table->dropForeign(['user_id']);
        $table->dropForeign(['role_id']);
      });

      Schema::table('users', function($table) {
        $table->dropForeign(['country_id']);
        $table->dropForeign(['plan_id']);
      });

      Schema::dropIfExists('plans');
      Schema::dropIfExists('countries');
      Schema::dropIfExists('users');
      Schema::dropIfExists('delivers');
      Schema::dropIfExists('categories');
      Schema::dropIfExists('discounts');
      Schema::dropIfExists('locations');
      Schema::dropIfExists('products');
      Schema::dropIfExists('images');
      Schema::dropIfExists('links');
      Schema::dropIfExists('password_resets');
      Schema::dropIfExists('roles');
      Schema::dropIfExists('role_user');
      Schema::dropIfExists('permissions');
      Schema::dropIfExists('permission_role');
      Schema::dropIfExists('tags');
      Schema::dropIfExists('product_tags');
    }
}
