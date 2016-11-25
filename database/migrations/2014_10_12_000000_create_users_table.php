<?php

use Illuminate\Support\Facades\Schema;
use Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function fields()
    {
        return ['id','name','email','password','fb_id','access_token','remember_token','updated_at','created_at'];
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->bigInteger('fb_id');
            $table->rememberToken();
            $table->string('access_token')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
