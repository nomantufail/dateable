<?php

use Illuminate\Support\Facades\Schema;
use Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function fields()
    {
        return ['id','first_name','last_name','email','gender','birthday','password','fb_id','access_token','remember_token','updated_at','created_at'];
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
            $table->string('first_name');
            $table->string('last_name');
            $table->tinyInteger('gender');
            $table->date('birthday');
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
